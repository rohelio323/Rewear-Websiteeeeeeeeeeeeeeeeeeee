@extends('layouts.admin')
@section('title','Dashboard')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:2rem;gap:1rem;flex-wrap:wrap;">
    <div>
        <h1 style="font-family:'Manrope',sans-serif;font-size:2rem;font-weight:800;color:#1A2820;letter-spacing:-0.03em;margin-bottom:0.25rem;">System Overview</h1>
        <p style="font-size:0.9375rem;color:#5A6B60;line-height:1.5;max-width:480px;">Real-time health of the ReWear ecosystem.</p>
    </div>
    <div style="display:flex;gap:10px;flex-shrink:0;">
        <button style="display:flex;align-items:center;gap:7px;background:#fff;border:1.5px solid #E2E2DE;color:#1A2820;padding:0.5rem 1rem;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#5A6B60" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export Report
        </button>
        <button style="display:flex;align-items:center;gap:7px;background:#1A2820;border:none;color:#fff;padding:0.5rem 1.125rem;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Announcement
        </button>
    </div>
</div>

{{-- KPI Cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
    @php
        $kpis = [
            ['label'=>'Total Users','value'=>number_format($totalUsers),'icon'=>'👥','bg'=>'#F0F5F2'],
            ['label'=>'Total Orders','value'=>number_format($totalOrders ?? 0),'icon'=>'📦','bg'=>'#FFF8F0'],
            ['label'=>'Total Listings','value'=>number_format($totalListings ?? 0),'icon'=>'🏷','bg'=>'#F0FAF5'],
        ];
    @endphp
    @foreach($kpis as $kpi)
    <div style="background:#fff;border:1.5px solid #E2E2DE;border-radius:14px;padding:1.25rem;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;">
            <span style="width:40px;height:40px;border-radius:10px;background:{{ $kpi['bg'] }};display:flex;align-items:center;justify-content:center;font-size:1.125rem;">{{ $kpi['icon'] }}</span>
        </div>
        <p style="font-size:0.75rem;color:#8A9E94;font-weight:500;margin-bottom:4px;">{{ $kpi['label'] }}</p>
        <p style="font-family:'Manrope',sans-serif;font-size:1.5rem;font-weight:800;color:#1A2820;letter-spacing:-0.02em;">{{ $kpi['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Sustainability row --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
    {{-- CO2 dark card --}}
    <div style="background:#1A2820;border-radius:14px;padding:1.5rem;position:relative;overflow:hidden;">
        <div style="position:absolute;right:1.5rem;top:50%;transform:translateY(-50%);opacity:0.12;">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="white"><path d="M3 3v18h18"/><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"/></svg>
        </div>
        <p style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.45);margin-bottom:0.5rem;">CO2 Saved Globally</p>
        <p style="font-family:'Manrope',sans-serif;font-size:2.75rem;font-weight:800;color:#fff;letter-spacing:-0.03em;line-height:1;margin-bottom:0.25rem;">{{ number_format($platformCo2/1000,1) }} <span style="font-size:1.75rem;">Tons</span></p>
        <p style="font-size:0.8125rem;color:rgba(255,255,255,0.5);margin-bottom:1rem;">Total CO2 emissions saved across the platform</p>
    </div>

    {{-- Water saved --}}
    <div style="background:#EBF8F2;border-radius:14px;padding:1.5rem;position:relative;overflow:hidden;">
        <div style="position:absolute;right:-10px;bottom:-10px;opacity:0.12;">
            <svg width="100" height="100" viewBox="0 0 24 24" fill="#059669"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
        </div>
        <p style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#059669;margin-bottom:0.5rem;">Total Water Saved</p>
        <p style="font-family:'Manrope',sans-serif;font-size:2.75rem;font-weight:800;color:#1A2820;letter-spacing:-0.03em;line-height:1;margin-bottom:0.25rem;">{{ number_format(($platformWater ?? 0)/1000,1) }} <span style="font-size:1.75rem;">kL</span></p>
        <p style="font-size:0.8125rem;color:#5A6B60;margin-bottom:1rem;">Saved by choosing pre-owned over new</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:1.25rem;margin-bottom:1.5rem;">
    {{-- Chart --}}
    <div style="background:#fff;border:1.5px solid #E2E2DE;border-radius:14px;padding:1.5rem;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;">
            <div>
                <h2 style="font-family:'Manrope',sans-serif;font-size:1.0625rem;font-weight:700;color:#1A2820;">Marketplace Activity</h2>
                <p style="font-size:0.8125rem;color:#8A9E94;">New listings over time</p>
            </div>
            <div style="display:flex;align-items:center;gap:12px;font-size:0.75rem;color:#5A6B60;font-weight:500;">
                <span style="display:flex;align-items:center;gap:5px;"><span style="width:8px;height:8px;border-radius:50%;background:#2D4739;display:inline-block;"></span>Listings</span>
            </div>
        </div>
        <canvas id="trendChart" height="160"></canvas>
    </div>

    {{-- Quick Links --}}
    <div style="background:#fff;border:1.5px solid #E2E2DE;border-radius:14px;padding:1.5rem;">
        <h2 style="font-family:'Manrope',sans-serif;font-size:1.0625rem;font-weight:700;color:#1A2820;margin-bottom:1.25rem;">Quick Actions</h2>

        <a href="{{ route('admin.users.index') }}" style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;background:#F0F5F2;border-radius:8px;text-decoration:none;margin-bottom:8px;">
            <span style="font-size:0.875rem;font-weight:600;color:#1A2820;">Manage Users</span>
            <span style="font-size:0.75rem;color:#5A6B60;">→</span>
        </a>

        <button style="width:100%;padding:0.5625rem;background:#fff;border:1.5px solid #E2E2DE;border-radius:8px;font-size:0.875rem;font-weight:600;color:#2D4739;cursor:pointer;margin-top:8px;">View All Reports</button>
    </div>
</div>

{{-- Directory Management --}}
<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <h2 style="font-family:'Manrope',sans-serif;font-size:1.0625rem;font-weight:700;color:#1A2820;">Recent Activity</h2>
        <a href="{{ route('admin.users.index') }}" style="padding:4px 12px;border-radius:6px;font-size:0.8125rem;font-weight:600;text-decoration:none;background:#1A2820;color:#fff;">View All Users</a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        @forelse($recentOrders->take(4) as $order)
        <div style="background:#fff;border:1.5px solid #E2E2DE;border-radius:12px;padding:1rem;display:flex;flex-direction:column;align-items:center;text-align:center;gap:8px;">
            <span style="width:40px;height:40px;border-radius:50%;background:#EBF0EC;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;color:#2D4739;">{{ strtoupper(substr($order->buyer?->name ?? '?', 0, 1)) }}</span>
            <div>
                <p style="font-size:0.8125rem;font-weight:600;color:#1A2820;">{{ Str::limit($order->buyer?->name ?? 'User', 16) }}</p>
                <p style="font-size:0.6875rem;color:#8A9E94;">{{ $order->created_at?->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <p style="color:#8A9E94;font-size:0.875rem;grid-column:span 4;">No recent activity.</p>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
const ctx = document.getElementById('trendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($trendLabels),
        datasets: [
            {
                label: 'Listings',
                data: @json($trendData),
                borderColor: '#2D4739',
                backgroundColor: 'rgba(45,71,57,0.06)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#2D4739',
                pointRadius: 3,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: 'JetBrains Mono', size: 10 }, color: '#8A9E94' } },
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'JetBrains Mono', size: 10 }, color: '#8A9E94' }, grid: { color: 'rgba(0,0,0,0.04)' } }
        }
    }
});
</script>
@endpush
