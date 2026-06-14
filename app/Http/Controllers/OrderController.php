<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Item;
use App\Models\VoucherRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['item_id' => 'required|exists:items,id']);

        $item = Item::with('category')->findOrFail($request->item_id);

        if ($item->users_id === Auth::id()) {
            return back()->with('error', 'You cannot buy your own listing.');
        }

        if ($item->status !== 'available') {
            return back()->with('error', 'This item is no longer available.');
        }

        $existing = Order::where('buyer_id', Auth::id())
            ->where('item_id', $item->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->route('orders.show', $existing)->with('info', 'You already have a pending order for this item.');
        }

        $order = DB::transaction(function () use ($item) {
            $order = Order::create([
                'buyer_id'         => Auth::id(),
                'item_id'          => $item->id,
                'status'           => 'pending',
                'total_price'      => $item->price,
                'users_id'         => $item->users_id,
                'co2_saved_amount' => $item->category->co2_constant ?? 0,
            ]);
            $item->update(['status' => 'reserved']);
            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order placed! Please confirm your payment.');
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load(['item.category', 'buyer', 'seller', 'review', 'voucherRedemption.voucher']);

        $redemptions = collect();

        if ($order->buyer_id === Auth::id() && $order->status === 'pending') {
            $redemptions = VoucherRedemption::where('user_id', Auth::id())
                ->where(function ($q) use ($order) {
                    $q->whereNull('order_id')->orWhere('order_id', $order->id);
                })
                ->with('voucher')
                ->latest()
                ->get();
        }

        return view('orders.show', compact('order', 'redemptions'));
    }

    public function applyVoucher(Request $request, Order $order)
    {
        abort_unless($order->buyer_id === Auth::id(), 403);
        abort_unless($order->status === 'pending', 403);

        $request->validate([
            'redemption_id' => 'required|exists:voucher_redemptions,id',
        ]);

        $redemption = VoucherRedemption::where('user_id', Auth::id())
            ->where('id', $request->redemption_id)
            ->where(function ($q) use ($order) {
                $q->whereNull('order_id')->orWhere('order_id', $order->id);
            })
            ->with('voucher')
            ->first();

        if (!$redemption) {
            return back()->with('error', 'Voucher not available.');
        }

        DB::transaction(function () use ($order, $redemption) {
            // If a different voucher was previously applied, release it
            if ($order->voucher_redemption_id && $order->voucher_redemption_id !== $redemption->id) {
                VoucherRedemption::where('id', $order->voucher_redemption_id)->update(['order_id' => null]);
            }

            $redemption->update(['order_id' => $order->id]);

            $order->update([
                'voucher_redemption_id' => $redemption->id,
                'discount_amount'       => $redemption->voucher->discount_amount,
            ]);
        });

        return back()->with('success', 'Voucher applied!');
    }

    public function removeVoucher(Order $order)
    {
        abort_unless($order->buyer_id === Auth::id(), 403);
        abort_unless($order->status === 'pending', 403);

        if ($order->voucher_redemption_id) {
            DB::transaction(function () use ($order) {
                VoucherRedemption::where('id', $order->voucher_redemption_id)->update(['order_id' => null]);
                $order->update(['voucher_redemption_id' => null, 'discount_amount' => 0]);
            });
        }

        return back()->with('success', 'Voucher removed.');
    }

    public function paymentForm(Order $order)
    {
        abort_unless($order->buyer_id === Auth::id(), 403);
        abort_unless($order->status === 'pending', 403);
        $order->load(['item']);
        return view('orders.payment', compact('order'));
    }

    public function confirmPayment(Request $request, Order $order)
    {
        abort_unless($order->buyer_id === Auth::id(), 403);
        abort_unless($order->status === 'pending', 403);

        $request->validate([
            'bank_name'         => 'required|string|max:100',
            'payment_reference' => 'required|string|max:100',
            'payment_proof'     => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        DB::transaction(function () use ($order, $request, $path) {
            $order->update([
                'status'            => 'payment_confirmed',
                'payment_reference' => $request->bank_name . ' - ' . $request->payment_reference,
                'payment_proof'     => $path,
            ]);

            $order->item->update(['status' => 'sold']);

            if ($order->voucher_redemption_id) {
                $order->update(['voucher_redemption_id' => null]);
            }
        });

        return redirect()->route('orders.confirmed', $order);
    }

    public function confirmed(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load(['item.category', 'buyer', 'seller', 'voucherRedemption.voucher']);
        return view('orders.confirmed', compact('order'));
    }

    public function shipForm(Order $order)
    {
        abort_unless($order->users_id === Auth::id(), 403);
        abort_unless($order->status === 'payment_confirmed', 403);
        $order->load(['item']);
        return view('orders.ship', compact('order'));
    }

    public function ship(Request $request, Order $order)
    {
        abort_unless($order->users_id === Auth::id(), 403);
        abort_unless($order->status === 'payment_confirmed', 403);

        $request->validate([
            'courier_name'    => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
            'shipping_proof'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('shipping_proof')->store('shipping_proofs', 'public');

        $order->update([
            'status'          => 'shipped',
            'tracking_number' => $request->courier_name . ' - ' . $request->tracking_number,
            'shipping_proof'  => $path,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Shipment confirmed! Waiting for buyer to confirm received.');
    }

    public function receive(Order $order)
    {
        abort_unless($order->buyer_id === Auth::id(), 403);
        abort_unless($order->status === 'shipped', 403);

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'completed']);

            $buyer = $order->buyer;
            if ($buyer) {
                $buyer->total_co2_saved += $order->co2_saved_amount;
                $buyer->save();
            }
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order completed and CO2 points added to your profile!');
    }

    public function cancel(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        DB::transaction(function () use ($order) {
            if ($order->voucher_redemption_id) {
                VoucherRedemption::where('id', $order->voucher_redemption_id)->update(['order_id' => null]);
            }
            $order->item->update(['status' => 'available']);
            $order->update(['status' => 'cancelled', 'voucher_redemption_id' => null, 'discount_amount' => 0]);
        });

        return redirect()->route('marketplace.index')->with('success', 'Order cancelled. Item is back on the marketplace.');
    }

    public function transactions()
    {
        $buying = Order::with(['item.category', 'seller'])
            ->where('buyer_id', Auth::id())
            ->latest()
            ->get();

        $selling = Order::with(['item.category', 'buyer'])
            ->where('users_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.transactions', compact('buying', 'selling'));
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless(
            $order->buyer_id === Auth::id() || $order->users_id === Auth::id(),
            403
        );
    }
}
