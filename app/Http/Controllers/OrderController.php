<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Item;
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
        $order->load(['item.category', 'buyer', 'seller']);
        return view('orders.show', compact('order'));
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

        $order->update([
            'status'            => 'payment_confirmed',
            'payment_reference' => $request->bank_name . ' — ' . $request->payment_reference,
            'payment_proof'     => $path,
        ]);

        $order->item->update(['status' => 'sold']);

        return redirect()->route('orders.confirmed', $order);
    }

    public function confirmed(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load(['item.category', 'buyer', 'seller']);
        return view('orders.confirmed', compact('order'));
    }

    public function ship(Order $order) // ← NEW
    {
        abort_unless($order->users_id === Auth::id(), 403);
        abort_unless($order->status === 'payment_confirmed', 403);

        $order->update(['status' => 'shipped']);

        return redirect()->route('orders.show', $order)->with('success', 'Shipment confirmed! Waiting for buyer to confirm received.');
    }

    public function receive(Order $order) // ← NEW
    {
        abort_unless($order->buyer_id === Auth::id(), 403);
        abort_unless($order->status === 'shipped', 403);

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'completed']);
        });

        return redirect()->route('orders.confirmed', $order)->with('success', 'Order completed!');
    }

    public function cancel(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        DB::transaction(function () use ($order) {
            $order->item->update(['status' => 'available']);
            $order->update(['status' => 'cancelled']);
        });

        return redirect()->route('marketplace.index')->with('success', 'Order cancelled. Item is back on the marketplace.');
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless(
            $order->buyer_id === Auth::id() || $order->users_id === Auth::id(),
            403
        );
    }
}