<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers      = User::count();
        $totalListings   = Item::where('status', 'available')->count();
        $totalOrders     = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $platformCo2     = User::sum('total_co2_saved');
        $totalPosts      = 0;

        $recentOrders = Order::with(['item', 'buyer', 'seller'])
            ->latest()
            ->take(10)
            ->get();

        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $days->put(now()->subDays($i)->format('Y-m-d'), 0);
        }


        $raw = Item::where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->orderBy('date')
            ->pluck('count', 'date');


        $trend = $days->merge($raw);


        $trendLabels = $trend->keys()->map(
            fn($d) => Carbon::parse($d)->format('M j')
        );
        $trendData = $trend->values();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalListings', 'totalOrders', 'completedOrders',
            'platformCo2', 'totalPosts', 'recentOrders', 'trendLabels', 'trendData'
        ));
    }
}
