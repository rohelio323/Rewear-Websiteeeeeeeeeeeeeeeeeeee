@extends('layouts.admin')
@section('title', 'User — '.$user->name)

@section('content')
<div style="max-width:700px;">
    <a href="{{ route('admin.users.index') }}" style="font-size:0.8125rem;color:var(--color-text-muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:1.5rem;">← Back to Users</a>

    <div class="section-header" style="margin-bottom:2rem;">
        <span class="section-header-overline">Users</span>
        <h1 class="section-title">{{ $user->name }}</h1>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
        @foreach([
            ['Email', $user->email, null],
            ['Role', null, $user->role],
            ['Verified Seller', $user->is_verified_seller ? 'Yes' : 'No', null],
            ['CO₂ Saved', number_format($user->total_co2_saved, 2).' kg', null],
            ['Member Since', $user->created_at->format('d M Y'), null],
            ['Status', $user->trashed() ? 'Deactivated' : 'Active', null],
        ] as [$label, $val, $rawBadge])
            <div class="card">
                <div style="padding:1rem;">
                    <p style="font-size:0.6875rem; ...">{{ $label }}</p>
                    @if($rawBadge !== null)
                        <x-status-badge :status="$rawBadge" />
                    @else
                        <p style="font-weight:600;font-size:1rem;">{{ $val }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem;">
        @foreach([
            ['Listings', $user->items->count()],
            ['Orders', $user->buyerOrders->count()]
        ] as [$l, $v])
        <div class="card" style="text-align:center;padding:1.25rem;">
            <p style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;color:var(--color-primary-900);">{{ $v }}</p>
            <p style="font-size:0.75rem;color:var(--color-text-muted);">{{ $l }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection
