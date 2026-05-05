@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto font-body">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-1 font-label">Overview</p>
            <h1 class="text-3xl font-extrabold text-emerald-950 tracking-tight font-headline">System Overview</h1>
            <p class="text-sm text-stone-500 mt-1">Real-time health of the ReWear ecosystem</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <button class="flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 rounded-xl text-sm font-bold transition-colors shadow-sm active:scale-95">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Export Report
            </button>
            <button class="flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-950 hover:bg-emerald-800 text-white rounded-xl text-sm font-bold transition-all shadow-sm hover:shadow active:scale-95">
                <span class="material-symbols-outlined text-[18px]">campaign</span>
                Announcement
            </button>
        </div>
    </div>

    {{-- KPI Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        @php
            $kpis = [
                ['label' => 'Total Users',    'value' => number_format($totalUsers),         'icon' => 'group',       'theme' => 'emerald'],
                ['label' => 'Total Orders',   'value' => number_format($totalOrders ?? 0),   'icon' => 'local_mall',  'theme' => 'amber'],
                ['label' => 'Total Listings', 'value' => number_format($totalListings ?? 0), 'icon' => 'sell',        'theme' => 'blue'],
            ];

            $themeMap = [
                'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-600'],
                'blue'    => ['bg' => 'bg-blue-50',    'text' => 'text-blue-600'],
            ];
        @endphp

        @foreach($kpis as $kpi)
            @php $t = $themeMap[$kpi['theme']]; @endphp
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-full {{ $t['bg'] }} flex items-center justify-center {{ $t['text'] }}">
                        <span class="material-symbols-outlined text-[24px]">{{ $kpi['icon'] }}</span>
                    </div>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-stone-400 mb-1 font-label">{{ $kpi['label'] }}</p>
                <p class="text-3xl font-extrabold text-stone-900 font-headline leading-none">{{ $kpi['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Sustainability Banner --}}
    <div class="mb-6">
        <div class="bg-gradient-to-br from-emerald-900 to-emerald-950 rounded-3xl p-8 md:p-10 relative overflow-hidden shadow-lg border border-emerald-800 flex flex-col justify-center min-h-[160px]">
            <div class="absolute -right-6 top-1/2 -translate-y-1/2 opacity-10 pointer-events-none transform -rotate-12">
                <span class="material-symbols-outlined text-[180px] text-white">eco</span>
            </div>
            <div class="relative z-10">
                <p class="text-[11px] font-bold uppercase tracking-widest text-emerald-400/80 mb-2 font-label">CO2 Saved Globally</p>
                <div class="flex items-baseline gap-2 mb-2">
                    <p class="text-5xl md:text-6xl font-extrabold text-white font-headline tracking-tight">{{ number_format($platformCo2 / 1000, 1) }}</p>
                    <span class="text-2xl font-semibold text-emerald-100">Tons</span>
                </div>
                <p class="text-sm text-emerald-200/80 max-w-md">Total CO₂ emissions saved globally by our community choosing pre-owned over new.</p>
            </div>
        </div>
    </div>

    {{-- Main Content Grid: Chart & Links --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Chart Section --}}
        <div class="lg:col-span-2 bg-white border border-stone-200 rounded-3xl p-6 md:p-8 shadow-sm">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-stone-900 font-headline">Marketplace Activity</h2>
                    <p class="text-sm text-stone-500">New listings over time</p>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-stone-50 rounded-lg border border-stone-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-800"></span>
                    <span class="text-xs font-semibold text-stone-600">Listings</span>
                </div>
            </div>
            <div class="relative w-full h-[240px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Quick Links Section --}}
        <div class="bg-white border border-stone-200 rounded-3xl p-6 md:p-8 shadow-sm flex flex-col">
            <h2 class="text-lg font-bold text-stone-900 font-headline mb-6">Quick Actions</h2>

            <div class="space-y-3 flex-1">
                <a href="{{ route('admin.users.index') }}" class="group flex items-center justify-between p-4 bg-stone-50 hover:bg-emerald-50 border border-stone-100 hover:border-emerald-100 rounded-2xl transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-stone-400 group-hover:text-emerald-600 transition-colors">manage_accounts</span>
                        <span class="text-sm font-bold text-stone-700 group-hover:text-emerald-800 transition-colors">Manage Users</span>
                    </div>
                    <span class="material-symbols-outlined text-stone-300 group-hover:text-emerald-500 transition-colors text-[20px] transform group-hover:translate-x-1">arrow_forward</span>
                </a>

                <a href="{{ route('admin.co2.index') }}" class="group flex items-center justify-between p-4 bg-stone-50 hover:bg-emerald-50 border border-stone-100 hover:border-emerald-100 rounded-2xl transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-stone-400 group-hover:text-emerald-600 transition-colors">category</span>
                        <span class="text-sm font-bold text-stone-700 group-hover:text-emerald-800 transition-colors">CO₂ Categories</span>
                    </div>
                    <span class="material-symbols-outlined text-stone-300 group-hover:text-emerald-500 transition-colors text-[20px] transform group-hover:translate-x-1">arrow_forward</span>
                </a>
            </div>

            <button class="w-full mt-4 py-3 bg-white border-2 border-stone-100 hover:border-stone-200 hover:bg-stone-50 rounded-xl text-sm font-bold text-stone-600 transition-colors">
                View All Reports
            </button>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div>
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-stone-900 font-headline">Recent Activity</h2>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 hover:underline transition-colors">View All Activity</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @forelse($recentOrders->take(4) as $order)
                <div class="bg-white border border-stone-200 rounded-2xl p-5 flex flex-col items-center text-center hover:shadow-md transition-shadow group">
                    <div class="w-12 h-12 rounded-full bg-stone-100 group-hover:bg-emerald-50 flex items-center justify-center text-lg font-bold text-stone-600 group-hover:text-emerald-700 transition-colors mb-3">
                        {{ strtoupper(substr($order->buyer?->name ?? '?', 0, 1)) }}
                    </div>
                    <p class="text-sm font-bold text-stone-900 truncate w-full">{{ Str::limit($order->buyer?->name ?? 'Unknown User', 16) }}</p>
                    <p class="text-xs text-stone-400 mt-1 font-mono">{{ $order->created_at?->diffForHumans() }}</p>
                </div>
            @empty
                <div class="col-span-full py-10 text-center bg-stone-50 rounded-2xl border border-stone-100 border-dashed">
                    <p class="text-sm font-medium text-stone-500">No recent activity found.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart').getContext('2d');
    let gradient = ctx.createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, 'rgba(6, 78, 59, 0.15)');
    gradient.addColorStop(1, 'rgba(6, 78, 59, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [
                {
                    label: 'New Listings',
                    data: @json($trendData),
                    borderColor: '#064e3b',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#064e3b',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1c1917',
                    padding: 12,
                    titleFont: { family: 'sans-serif', size: 13 },
                    bodyFont: { family: 'sans-serif', size: 13, weight: 'bold' },
                    displayColors: false,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'monospace', size: 11 }, color: '#a8a29e' },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { family: 'monospace', size: 11 }, color: '#a8a29e' },
                    grid: { color: '#f5f5f4', drawBorder: false },
                    border: { display: false }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
});
</script>
@endpush
