@extends('layouts.admin')
@section('title','User Moderation')

@section('content')

{{-- Header --}}
<div class="flex items-end justify-between mb-10 flex-wrap gap-4">
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-1 font-label">Admin</p>
        <h1 class="text-3xl font-extrabold text-emerald-950 tracking-tight font-headline">Active Community Members</h1>
        <p class="text-sm text-stone-500 mt-1 font-body">Manage and review users on the platform.</p>
    </div>
    <form method="GET" class="flex gap-2 font-body">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search name or email..."
            class="border border-stone-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white w-60">
        <button type="submit" class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-stone-200 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition">
            <span class="material-symbols-outlined text-base">filter_list</span> Filter
        </button>
        @if(!empty($search))
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl bg-white border border-stone-200 text-sm font-semibold text-stone-500 hover:bg-stone-50 transition">Clear</a>
        @endif
    </form>
</div>

{{-- Stats Grid --}}
<div class="flex flex-wrap gap-3 mb-10">
    @php
        $statCards = [
            ['label' => 'Total Users',      'value' => number_format($users->total()),                   'icon' => 'group',         'color' => 'emerald'],
            ['label' => 'Verified Sellers', 'value' => number_format($stats['is_verified_seller'] ?? 0), 'icon' => 'sell',          'color' => 'emerald'],
            ['label' => 'Pending Sellers',  'value' => number_format($stats['pending_sellers'] ?? 0),    'icon' => 'schedule',      'color' => 'amber'],
            ['label' => 'Flagged',          'value' => number_format($stats['flagged'] ?? 0),            'icon' => 'priority_high', 'color' => 'red'],
        ];
        $colorMap = [
            'emerald' => ['border' => 'border-stone-200',  'value' => 'text-emerald-950', 'icon' => 'text-stone-400'],
            'amber'   => ['border' => 'border-amber-200',  'value' => 'text-amber-700',   'icon' => 'text-amber-500'],
            'red'     => ['border' => 'border-red-200',    'value' => 'text-red-700',     'icon' => 'text-red-400'],
        ];
    @endphp

    @foreach($statCards as $card)
        @php $c = $colorMap[$card['color']]; @endphp
        <div class="bg-white rounded-xl border {{ $c['border'] }} px-4 py-3 flex items-center gap-3">
            <span class="material-symbols-outlined text-lg {{ $c['icon'] }}">{{ $card['icon'] }}</span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 leading-none mb-1 font-label">{{ $card['label'] }}</p>
                <p class="text-xl font-extrabold {{ $c['value'] }} leading-none font-headline">{{ $card['value'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-stone-200 overflow-hidden font-body">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-stone-100 bg-stone-50">
                    @foreach(['#','Name','Email','Role','Seller','CO₂','Status','Seller Request','Joined','Actions'] as $h)
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-widest text-stone-400 whitespace-nowrap font-label">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($users as $user)
                <tr class="hover:bg-stone-50 transition-colors {{ $user->trashed() ? 'opacity-50' : '' }}">

                    {{-- ID --}}
                    <td class="px-5 py-4 text-xs text-stone-400 font-mono">{{ $user->id }}</td>

                    {{-- Name --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-content-center flex-shrink-0 overflow-hidden flex items-center justify-center font-bold text-emerald-800 text-sm">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="font-bold text-emerald-950 hover:text-emerald-700 transition-colors no-underline font-headline">{{ $user->name }}</a>
                            </div>
                        </div>
                    </td>

                    {{-- Email --}}
                    <td class="px-5 py-4 text-stone-500 text-xs">{{ $user->email }}</td>

                    {{-- Role --}}
                    <td class="px-5 py-4">
                        @if($user->role === 'admin')
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wide font-label">Admin</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-stone-100 text-stone-600 text-[10px] font-bold uppercase tracking-wide font-label">User</span>
                        @endif
                    </td>

                    {{-- Verified Seller --}}
                    <td class="px-5 py-4">
                        @if($user->is_verified_seller)
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black">✓</span>
                        @else
                            <span class="text-stone-300">—</span>
                        @endif
                    </td>

                    {{-- CO2 --}}
                    <td class="px-5 py-4">
                        <span class="inline-block px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-mono text-xs font-bold">
                            {{ number_format($user->total_co2_saved ?? 0, 2) }} kg
                        </span>
                    </td>

                    {{-- Account Status --}}
                    <td class="px-5 py-4">
                        @if($user->trashed())
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wide font-label">Suspended</span>
                        @elseif($user->is_flagged ?? false)
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-orange-100 text-orange-700 text-[10px] font-bold uppercase tracking-wide font-label">Flagged</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wide font-label">Active</span>
                        @endif
                    </td>

                    {{-- Seller Request --}}
                    <td class="px-5 py-4">
                        @if($user->seller_request_status === 'pending')
                            <div class="flex flex-col gap-2 items-start">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-wide font-label">
                                    <span class="material-symbols-outlined text-[10px]">schedule</span> Pending
                                </span>
                                <div class="flex gap-1.5">
                                    <form method="POST" action="{{ route('admin.seller-requests.approve', $user->id) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <button type="button"
                                        class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold border border-red-200 transition-colors"
                                        onclick="document.getElementById('reject-modal-{{ $user->id }}').classList.remove('hidden')">
                                        Reject
                                    </button>
                                </div>
                            </div>

                            {{-- Reject Modal --}}
                            <div id="reject-modal-{{ $user->id }}"
                                class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 font-body">
                                <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-stone-200">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-red-600">cancel</span>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-emerald-950 text-base font-headline">Reject Seller Request</h3>
                                            <p class="text-xs text-stone-400">{{ $user->name }} will be notified with your reason.</p>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('admin.seller-requests.reject', $user->id) }}">
                                        @csrf
                                        <textarea name="note" required placeholder="e.g. Incomplete profile information..."
                                            class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm resize-none h-24 focus:outline-none focus:ring-2 focus:ring-red-400 mb-4"></textarea>
                                        <div class="flex gap-2 justify-end">
                                            <button type="button"
                                                class="px-4 py-2 rounded-xl border border-stone-200 text-sm font-semibold text-stone-600 hover:bg-stone-50 transition"
                                                onclick="document.getElementById('reject-modal-{{ $user->id }}').classList.add('hidden')">
                                                Cancel
                                            </button>
                                            <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition">
                                                Confirm Reject
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        @elseif($user->seller_request_status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wide font-label">
                                <span class="material-symbols-outlined text-[10px]">verified</span> Approved
                            </span>
                        @elseif($user->seller_request_status === 'rejected')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wide font-label">
                                <span class="material-symbols-outlined text-[10px]">cancel</span> Rejected
                            </span>
                        @else
                            <span class="text-stone-300">—</span>
                        @endif
                    </td>

                    {{-- Joined --}}
                    <td class="px-5 py-4 text-stone-400 text-xs font-mono whitespace-nowrap">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.users.show', $user->id) }}"
                               class="p-1.5 rounded-lg hover:bg-stone-100 text-stone-500 hover:text-emerald-700 transition-colors"
                               title="View">
                                <span class="material-symbols-outlined text-base">visibility</span>
                            </a>
                            @if($user->trashed())
                                <form method="POST" action="{{ route('admin.users.restore', $user->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition-colors">
                                        Restore
                                    </button>
                                </form>
                            @elseif($user->role !== 'admin')
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                      onsubmit="return confirm('Suspend {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold border border-red-200 transition-colors">
                                        Suspend
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-5 py-16 text-center text-stone-400 text-sm font-body">
                        <span class="material-symbols-outlined text-4xl block mb-2 text-stone-300">group_off</span>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-5 py-4 flex items-center justify-between border-t border-stone-100 flex-wrap gap-3">
        <p class="text-xs text-stone-400 font-mono">
            Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ number_format($users->total()) }} users
        </p>
        <div>{{ $users->links() }}</div>
    </div>
</div>

@endsection
