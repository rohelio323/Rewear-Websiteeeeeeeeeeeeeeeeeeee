<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Tampilkan form review
    public function create(Order $order)
    {
        // Pastikan hanya buyer yang bisa review
        if (Auth::id() !== $order->buyer_id) {
            abort(403);
        }

        // Pastikan order sudah completed
        if ($order->status !== 'completed') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You can only review completed orders.');
        }

        // Cek sudah pernah review belum
        if ($order->review) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You have already reviewed this order.');
        }

        return view('reviews.create', compact('order'));
    }

    // Simpan review
    public function store(Request $request, Order $order)
    {
        if (Auth::id() !== $order->buyer_id) {
            abort(403);
        }

        if ($order->status !== 'completed') {
            abort(403);
        }

        if ($order->review) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'You have already reviewed this order.');
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'order_id'  => $order->id,
            'buyer_id'  => Auth::id(),
            'seller_id' => $order->users_id,
            'item_id'   => $order->item_id,
            'rating'    => $request->rating,
            'comment'   => $request->comment,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Thank you for your review!');
    }

    public function index()
    {
        $reviews = Auth::user()->reviewsReceivedList()->paginate(10);
        $avgRating = Auth::user()->averageRating();
        $totalReviews = Auth::user()->totalReviews();

        return view('reviews.index', compact('reviews', 'avgRating', 'totalReviews'));
    }
    
    public function sellerProfile(\App\Models\User $user)
    {
        $reviews = $user->reviewsReceived()
            ->with(['item', 'buyer'])
            ->latest()
            ->paginate(10);

        $avgRating = $user->averageRating();
        $totalReviews = $user->totalReviews();

        return view('reviews.seller-profile', compact('user', 'reviews', 'avgRating', 'totalReviews'));
    }
}