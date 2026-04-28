@extends('layouts.admin')
@section('title','User Moderation')

@section('content')
{{-- Header Section --}}
<div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:2.5rem;flex-wrap:wrap;gap:1rem;">
    <div class="section-header">
        <span class="section-header-overline">Admin</span>
        <h1 class="section-title">Active Community Members</h1>
        <p style="color:var(--color-text-muted);font-size:0.875rem;margin-top:0.25rem;">
            Manage and review users on the platform.
        </p>
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
        <form method="GET" style="display:flex;gap:8px;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search name or email..." class="form-input" style="width:240px;">
            <button type="submit" class="btn btn-secondary">
                <span class="material-symbols-outlined" style="font-size:1rem;vertical-align:middle;">filter_list</span>
                Filter
            </button>
            @if(!empty($search))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Stats Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;margin-bottom:2.5rem;">
    <div class="card" style="padding:1.5rem;">
        <div class="card-accent-bar"></div>
        <p style="font-size:0.5625rem;font-family:'JetBrains Mono',monospace;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-text-muted);margin-bottom:0.5rem;">Total Users</p>
        <h3 style="font-size:2rem;font-weight:800;color:var(--color-primary-900);margin-bottom:0.5rem;">{{ number_format($users->total()) }}</h3>
        <div style="display:flex;align-items:center;gap:0.25rem;color:var(--color-text-muted);font-size:0.75rem;">
            <span class="material-symbols-outlined" style="font-size:0.875rem;">group</span>
            <span>All registered members</span>
        </div>
    </div>

    <div class="card" style="padding:1.5rem;">
        <div class="card-accent-bar"></div>
        <p style="font-size:0.5625rem;font-family:'JetBrains Mono',monospace;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-text-muted);margin-bottom:0.5rem;">Verified Sellers</p>
        <h3 style="font-size:2rem;font-weight:800;color:var(--color-primary-900);margin-bottom:0.5rem;">{{ number_format($stats['is_verified_seller'] ?? 0) }}</h3>
        <div style="display:flex;align-items:center;gap:0.25rem;color:var(--color-text-muted);font-size:0.75rem;">
            <span class="material-symbols-outlined" style="font-size:0.875rem;">sell</span>
            <span>Approved to sell</span>
        </div>
    </div>

    <div class="card" style="padding:1.5rem;">
        <div class="card-accent-bar"></div>
        <p style="font-size:0.5625rem;font-family:'JetBrains Mono',monospace;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-text-muted);margin-bottom:0.5rem;">Flagged</p>
        <h3 style="font-size:2rem;font-weight:800;color:var(--color-primary-900);margin-bottom:0.5rem;">{{ number_format($stats['flagged'] ?? 0) }}</h3>
        <div style="display:flex;align-items:center;gap:0.25rem;color:var(--color-text-muted);font-size:0.75rem;">
            <span class="material-symbols-outlined" style="font-size:0.875rem;">priority_high</span>
            <span>Requires attention</span>
        </div>
    </div>

    <div class="card" style="padding:1.5rem;">
        <div class="card-accent-bar"></div>
        <p style="font-size:0.5625rem;font-family:'JetBrains Mono',monospace;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-text-muted);margin-bottom:0.5rem;">New Today</p>
        <h3 style="font-size:2rem;font-weight:800;color:var(--color-primary-900);margin-bottom:0.5rem;">{{ number_format($stats['new_today'] ?? 0) }}</h3>
        <div style="display:flex;align-items:center;gap:0.25rem;color:var(--color-text-muted);font-size:0.75rem;">
            <span class="material-symbols-outlined" style="font-size:0.875rem;">person_add</span>
            <span>Registered today</span>
        </div>
    </div>
</div>

{{-- Moderation Table --}}
<div class="card">
    <div class="card-accent-bar"></div>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:1px solid var(--color-border);">
                @foreach(['#','Name','Email','Role','Verified','CO₂ Saved','Status','Joined','Actions'] as $h)
                    <th style="padding:0.875rem 1rem;text-align:left;font-size:0.5625rem;font-family:'JetBrains Mono',monospace;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-text-muted);">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr style="border-bottom:1px solid var(--color-border);{{ $user->trashed() ? 'opacity:0.55;' : '' }}">
                <td style="padding:0.875rem 1rem;font-family:'JetBrains Mono',monospace;font-size:0.75rem;color:var(--color-text-muted);">
                    {{ $user->id }}
                </td>
                <td style="padding:0.875rem 1rem;">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div style="width:2.5rem;height:2.5rem;border-radius:9999px;overflow:hidden;background:var(--color-primary-100);flex-shrink:0;">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;color:var(--color-primary-900);font-weight:700;font-size:0.875rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('admin.users.show', $user->id) }}" style="font-weight:600;text-decoration:none;color:var(--color-primary-900);">
                            {{ $user->name }}
                        </a>
                    </div>
                </td>
                <td style="padding:0.875rem 1rem;font-size:0.8125rem;color:var(--color-text-muted);">
                    {{ $user->email }}
                </td>
                <td style="padding:0.875rem 1rem;">
                    <x-status-badge :status="$user->role" />
                </td>
                <td style="padding:0.875rem 1rem;">
                    @if($user->is_verified_seller)
                        <span class="badge badge-verified">✓</span>
                    @else
                        <span style="color:var(--color-text-subtle);">—</span>
                    @endif
                </td>
                <td style="padding:0.875rem 1rem;font-family:'JetBrains Mono',monospace;font-size:0.875rem;font-weight:700;color:var(--color-primary-700);">
                    {{ number_format($user->total_co2_saved ?? 0, 1) }} kg
                </td>
                <td style="padding:0.875rem 1rem;">
                    @if($user->trashed())
                        <span class="badge badge-cancelled">Suspended</span>
                    @elseif($user->is_flagged)
                        <span class="badge badge-flagged">Flagged</span>
                    @else
                        <span class="badge badge-available">Active</span>
                    @endif
                </td>
                <td style="padding:0.875rem 1rem;font-size:0.8125rem;color:var(--color-text-muted);">
                    {{ $user->created_at->format('M d, Y') }}
                </td>
                <td style="padding:0.875rem 1rem;">
                    <div style="display:flex;gap:6px;align-items:center;">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary" style="padding:0.25rem 0.5rem;font-size:0.75rem;" title="View Activity">
                            <span class="material-symbols-outlined" style="font-size:1rem;">visibility</span>
                        </a>
                        @if($user->trashed())
                            <form method="POST" action="{{ route('admin.users.restore', $user->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary" style="padding:0.25rem 0.625rem;font-size:0.75rem;" title="Restore">
                                    Restore
                                </button>
                            </form>
                        @elseif($user->role !== 'admin')
                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Suspend this user?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding:0.25rem 0.625rem;font-size:0.75rem;" title="Suspend">
                                    Suspend
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="padding:2rem 1rem;text-align:center;color:var(--color-text-muted);">
                    No users found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div style="padding:1rem;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--color-border);flex-wrap:wrap;gap:1rem;">
        <p style="font-size:0.75rem;color:var(--color-text-muted);font-family:'JetBrains Mono',monospace;">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ number_format($users->total()) }} users
        </p>
        <div>{{ $users->links() }}</div>
    </div>
</div>
@endsection
