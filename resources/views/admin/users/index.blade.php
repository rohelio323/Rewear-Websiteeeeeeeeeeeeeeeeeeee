@extends('layouts.admin')
@section('title','User Moderation')

@section('content')

{{-- Header & Search --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 font-body">
    <div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-1 font-label">Administration</p>
        <h1 class="text-3xl font-extrabold text-emerald-950 tracking-tight font-headline">Community Members</h1>
        <p class="text-sm text-stone-500 mt-1">Manage platform users, verify sellers, and oversee community health.</p>
    </div>
    
    <form method="GET" class="flex items-center gap-2">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-[20px]">search</span>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search name or email..."
                class="border border-stone-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white w-full md:w-64 transition-shadow">
        </div>
        <button type="submit" class="flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-stone-200 text-stone-600 hover:bg-stone-50 hover:text-emerald-700 transition-colors" title="Search">
            <span class="material-symbols-outlined text-[20px]">filter_list</span>
        </button>
        @if(!empty($search))
            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-center w-11 h-11 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-100 transition-colors" title="Clear Search">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </a>
        @endif
    </form>
</div>

{{-- Stats Grid (Refined for premium look) --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 font-body">
    @php
        $statCards = [
            ['label' => 'Total Users',      'value' => number_format($users->total()),                   'icon' => 'group',         'theme' => 'emerald'],
            ['label' => 'Verified Sellers', 'value' => number_format($stats['is_verified_seller'] ?? 0), 'icon' => 'verified',      'theme' => 'emerald'],
            ['label' => 'Pending Requests', 'value' => number_format($stats['pending_sellers'] ?? 0),    'icon' => 'hourglass_top', 'theme' => 'amber'],
            ['label' => 'Flagged / Suspended', 'value' => number_format($stats['flagged'] ?? 0),         'icon' => 'warning',       'theme' => 'red'],
        ];
        $colorMap = [
            'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'hover:border-emerald-200'],
            'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'border' => 'hover:border-amber-200'],
            'red'     => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'border' => 'hover:border-red-200'],
        ];
    @endphp

    @foreach($statCards as $card)
        @php $c = $colorMap[$card['theme']]; @endphp
        <div class="bg-white rounded-2xl border border-stone-200 p-5 flex items-center gap-4 {{ $c['border'] }} hover:shadow-sm transition-all group">
            <div class="w-12 h-12 rounded-full {{ $c['bg'] }} flex items-center justify-center {{ $c['text'] }}">
                <span class="material-symbols-outlined">{{ $card['icon'] }}</span>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-0.5 font-label">{{ $card['label'] }}</p>
                <p class="font-headline text-2xl font-extrabold text-stone-900 leading-none">{{ $card['value'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- Consolidated Table --}}
<div class="bg-white rounded-2xl border border-stone-200 overflow-hidden font-body shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50/50">
                    {{-- Reduced from 10 columns to 6 for much better readability --}}
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">User</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">Role & Status</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">Seller Status</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">Impact</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">Joined</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($users as $user)
                <tr class="hover:bg-stone-50/80 transition-colors {{ $user->trashed() ? 'opacity-60 bg-stone-50' : '' }} group">

                    {{-- Column 1: Consolidated Avatar, Name, Email, ID --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 overflow-hidden font-bold text-emerald-800 text-sm">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="flex flex-col">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="font-bold text-stone-900 hover:text-emerald-700 transition-colors font-headline truncate max-w-[200px]">{{ $user->name }}</a>
                                <span class="text-[11px] text-stone-500 truncate max-w-[200px]">{{ $user->email }}</span>
                                <span class="text-[9px] text-stone-400 font-mono mt-0.5">ID: {{ $user->id }}</span>
                            </div>
                        </div>
                    </td>

                    {{-- Column 2: Consolidated Role and Status --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1.5 items-start">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-2 py-0.5 rounded border border-purple-200 bg-purple-50 text-purple-700 text-[10px] font-bold uppercase tracking-wide font-label">Admin</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded border border-stone-200 bg-stone-50 text-stone-600 text-[10px] font-bold uppercase tracking-wide font-label">User</span>
                            @endif

                            @if($user->trashed())
                                <span class="inline-flex items-center gap-1 text-red-600 text-xs font-medium"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspended</span>
                            @elseif($user->is_flagged ?? false)
                                <span class="inline-flex items-center gap-1 text-amber-600 text-xs font-medium"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Flagged</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-emerald-600 text-xs font-medium"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>
                            @endif
                        </div>
                    </td>

                    {{-- Column 3: Consolidated Seller Request & Verification --}}
                    <td class="px-6 py-4">
                        @if($user->is_verified_seller || $user->seller_request_status === 'approved')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-bold font-label">
                                <span class="material-symbols-outlined text-[14px]">verified</span> Verified Seller
                            </span>
                        @elseif($user->seller_request_status === 'pending')
                            <div class="flex flex-col gap-2 items-start">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-wide font-label">
                                    Pending Request
                                </span>
                                <div class="flex gap-1">
                                    <form method="POST" action="{{ route('admin.seller-requests.approve', $user->id) }}">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold transition-colors">Approve</button>
                                    </form>
                                    <button type="button" class="px-2 py-1 rounded bg-white hover:bg-red-50 text-red-600 border border-red-200 text-[10px] font-bold transition-colors" onclick="document.getElementById('reject-modal-{{ $user->id }}').classList.remove('hidden')">Reject</button>
                                </div>
                            </div>
                        @elseif($user->seller_request_status === 'rejected')
                            <span class="inline-flex items-center gap-1 text-red-500 text-xs font-medium"><span class="material-symbols-outlined text-[14px]">cancel</span> Rejected</span>
                        @else
                            <span class="text-stone-300">—</span>
                        @endif

                        {{-- Reject Modal (Kept exactly where it belongs) --}}
                        @if($user->seller_request_status === 'pending')
                        <div id="reject-modal-{{ $user->id }}" class="hidden fixed inset-0 bg-stone-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 font-body">
                            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-stone-200 transform transition-all">
                                <div class="flex items-center gap-3 mb-5">
                                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-red-600">block</span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-stone-900 text-lg font-headline leading-tight">Reject Seller Request</h3>
                                        <p class="text-xs text-stone-500">{{ $user->name }} will be notified.</p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.seller-requests.reject', $user->id) }}">
                                    @csrf
                                    <textarea name="note" required placeholder="Reason for rejection (e.g., Incomplete profile...)" class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm resize-none h-24 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 mb-5 text-stone-700"></textarea>
                                    <div class="flex gap-3 justify-end">
                                        <button type="button" class="px-4 py-2 rounded-xl text-sm font-semibold text-stone-500 hover:bg-stone-100 transition" onclick="document.getElementById('reject-modal-{{ $user->id }}').classList.add('hidden')">Cancel</button>
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition shadow-sm shadow-red-200">Confirm Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                    </td>

                    {{-- Column 4: Impact (CO2) --}}
                    <td class="px-6 py-4">
                        <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-emerald-50 border border-emerald-100/50 text-emerald-800 font-mono text-xs font-medium" title="Total CO₂ emission saved">
                            <span class="material-symbols-outlined text-[14px] text-emerald-600">eco</span>
                            {{ number_format($user->total_co2_saved ?? 0, 1) }} kg
                        </div>
                    </td>

                    {{-- Column 5: Joined Date --}}
                    <td class="px-6 py-4 text-stone-500 text-xs whitespace-nowrap">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>

                    {{-- Column 6: Actions --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="p-1.5 rounded-lg text-stone-400 hover:bg-emerald-50 hover:text-emerald-700 transition-colors" title="View Profile">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </a>
                            
                            @if($user->trashed())
                                <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-100 transition-colors" title="Restore User">
                                        <span class="material-symbols-outlined text-[20px]">settings_backup_restore</span>
                                    </button>
                                </form>
                            @elseif($user->role !== 'admin')
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline" onsubmit="return confirm('Suspend {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Suspend User">
                                        <span class="material-symbols-outlined text-[20px]">person_off</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center text-stone-400 text-sm font-body">
                        <div class="w-16 h-16 mx-auto bg-stone-50 rounded-full flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-3xl text-stone-300">group_off</span>
                        </div>
                        <p class="font-medium text-stone-500">No users found.</p>
                        <p class="text-xs mt-1">Try adjusting your search criteria.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (Cleaned up) --}}
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-stone-100 bg-white">
        {{ $users->links() }}
    </div>
    @else
    <div class="px-6 py-4 border-t border-stone-100 bg-white text-xs text-stone-400 font-mono text-center">
        Total {{ number_format($users->total()) }} users
    </div>
    @endif
</div>

@endsection