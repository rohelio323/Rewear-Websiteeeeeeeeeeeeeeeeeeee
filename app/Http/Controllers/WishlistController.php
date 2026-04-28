<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Tampilkan halaman wishlist user
    public function index()
    {
        $items = Auth::user()->favorites()
            ->with(['category', 'user'])
            ->latest('item_user.created_at')
            ->paginate(12);

        // This line is the key — same as ItemController
        Auth::user()->load('favorites');

        return view('favorites.index', compact('items'));
    }

    // Toggle add/remove wishlist (support AJAX + non-AJAX)
    public function toggle(Item $item)
    {
        $user = Auth::user();
        $result = $user->favorites()->toggle($item->id);
        $favorited = count($result['attached']) > 0;

        if (!request()->expectsJson()) {
            return back()->with('success', $favorited
                ? 'Item ditambahkan ke wishlist!'
                : 'Item dihapus dari wishlist.');
        }

        return response()->json([
            'favorited' => $favorited,
            'count'     => $item->favoritedBy()->count(),
        ]);
    }
}