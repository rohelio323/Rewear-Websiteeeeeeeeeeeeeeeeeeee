<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;


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

        // Precompute all chart periods — view switches instantly without AJAX
        $allPeriods = ['7', '30', 'q1', 'q2', 'q3', 'q4'];
        $chartJson  = [];
        foreach ($allPeriods as $p) {
            [$labels, $data] = $this->buildChartData($p);
            $chartJson[$p]   = ['labels' => $labels->values(), 'data' => $data->values()];
        }

        // Default display: 30-day daily view
        $trendLabels = collect($chartJson['30']['labels']);
        $trendData   = collect($chartJson['30']['data']);

        return view('admin.dashboard', compact(
            'totalUsers', 'totalListings', 'totalOrders', 'completedOrders',
            'platformCo2', 'totalPosts', 'recentOrders', 'trendLabels', 'trendData', 'chartJson'
        ));
    }

    /**
     * JSON endpoint — kept for potential external use.
     * Query param: period = '7' | '30' | 'q1' | 'q2' | 'q3' | 'q4'
     */
    public function chartData(Request $request)
    {
        $period = $request->query('period', '30');
        if (!in_array($period, ['7', '30', 'q1', 'q2', 'q3', 'q4'])) {
            $period = '30';
        }

        [$labels, $data] = $this->buildChartData($period);

        return response()->json([
            'labels' => $labels->values(),
            'data'   => $data->values(),
        ]);
    }

    /**
     * Build chart labels and data.
     *
     * @param  string $period  '7' | '30' | 'q1' | 'q2' | 'q3' | 'q4'
     * @return array  [Collection $labels, Collection $data]
     */
    private function buildChartData(string $period): array
    {
        // ---- Quarter (Q1–Q4) ------------------------------------------------
        if (in_array($period, ['q1', 'q2', 'q3', 'q4'])) {
            $quarterNum        = (int) substr($period, 1);       // 1..4
            $quarterStartMonth = ($quarterNum - 1) * 3 + 1;      // 1, 4, 7, 10
            $year              = Carbon::now()->year;
            $quarterStart      = Carbon::create($year, $quarterStartMonth, 1)->startOfDay();
            $quarterEnd        = $quarterStart->copy()->addMonths(3); // exclusive

            // Zero-filled skeleton: 3 months
            $months = collect();
            for ($i = 0; $i < 3; $i++) {
                $months->put($quarterStart->copy()->addMonths($i)->format('Y-m'), 0);
            }

            $raw = Item::where('created_at', '>=', $quarterStart)
                ->where('created_at', '<', $quarterEnd)
                ->groupBy('month')
                ->select(DB::raw("strftime('%Y-%m', created_at) as month"), DB::raw('count(*) as count'))
                ->orderBy('month')
                ->pluck('count', 'month');

            $trend  = $months->merge($raw);
            $labels = $trend->keys()->map(fn($m) => Carbon::parse($m . '-01')->format('F'));
            $data   = $trend->values();

            return [$labels, $data];
        }

        // ---- Daily (7 or 30 days) -------------------------------------------
        $days = (int) $period;

        $skeleton = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $skeleton->put(Carbon::now()->subDays($i)->format('Y-m-d'), 0);
        }

        $raw = Item::where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->orderBy('date')
            ->pluck('count', 'date');

        $trend  = $skeleton->merge($raw);
        $labels = $trend->keys()->map(fn($d) => Carbon::parse($d)->format('M j'));
        $data   = $trend->values();

        return [$labels, $data];
    }
}
