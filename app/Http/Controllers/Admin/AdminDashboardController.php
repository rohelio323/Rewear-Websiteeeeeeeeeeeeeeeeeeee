<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers       = User::count();
        $totalListings    = Item::count();
        $totalOrders      = Order::count();
        $completedOrders  = Order::where('status', 'completed')->count();
        $platformCo2      = User::sum('total_co2_saved');
        $totalPosts       = 0;

        $recentOrders = Order::with(['item', 'buyer', 'seller'])
            ->latest()
            ->take(10)
            ->get();


        $trend = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->orderBy('date')
            ->get();

        $trendLabels = $trend->pluck('date');
        $trendData   = $trend->pluck('count');

        return view('admin.dashboard', compact(
            'totalUsers', 'totalListings', 'totalOrders', 'completedOrders',
            'platformCo2', 'totalPosts', 'recentOrders', 'trendLabels', 'trendData'
        ));
    }
}
