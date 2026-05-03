<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $users = User::withTrashed()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_users'       => User::count(),
            'is_verified_seller' => User::where('is_verified_seller', 1)->count(),
            'deactivated_users' => User::onlyTrashed()->count(),
            'pending_sellers'    => User::where('seller_request_status', 'pending')->count(),
        ];

        return view('admin.users.index', compact('users', 'search', 'stats'));
    }


    public function show(User $user)
    {
        $user->load(['items', 'buyerOrders', 'sellerOrders']);
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete(); // SoftDelete
        return redirect()->route('admin.users.index')->with('success', 'User deactivated.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.index')->with('success', 'User restored.');
    }

    public function approveSeller(User $user): \Illuminate\Http\RedirectResponse
    {
        $user->approveAsSeller();
        return redirect()->route('admin.users.index')->with('success', "{$user->name} has been approved as a seller.");
    }

    public function rejectSeller(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['note' => 'required|string|max:500']);
        $user->rejectAsSeller($request->note);
        return redirect()->route('admin.users.index')->with('success', "{$user->name}'s seller request has been rejected.");
    }
}
