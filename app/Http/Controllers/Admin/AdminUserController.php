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
}
