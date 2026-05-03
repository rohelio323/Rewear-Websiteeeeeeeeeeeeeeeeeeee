@extends('layouts.admin')
@section('title', 'User — '.$user->name)

@section('content')
<div class="max-w-4xl">

    {{-- Back Link --}}
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-stone-500 hover:text-emerald-700 transition-colors mb-10 font-body">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Back to Users
    </a>

    {{-- Simple Profile Header --}}
    <div class="flex items-center gap-4 mb-10 font-body">

        {{-- The Avatar Circle (Hardcoded dimensions to prevent squishing) --}}
        <div style="width: 48px; height: 48px; flex-shrink: 0;" class="rounded-full bg-emerald-100 flex items-center justify-center text-emerald-900 text-lg font-bold">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover rounded-full" alt="{{ $user->name }}">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>

        {{-- Name --}}
        <h1 class="text-2xl font-semibold text-stone-900 tracking-tight">{{ $user->name }}</h1>

        {{-- Badges --}}
        <div class="ml-2 flex items-center gap-2">
            @if($user->is_verified_seller)
                <span class="material-symbols-outlined text-emerald-600 text-xl" title="Verified Seller">verified</span>
            @endif

            @if($user->role === 'admin')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wide font-label">Admin</span>
            @endif
        </div>
    </div>

    {{-- User Details & Activity Strip --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 font-body">

        {{-- Member Status --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5 flex flex-col justify-center">
            <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2 font-label">Account Info</p>
            <p class="text-sm font-medium text-emerald-950 truncate mb-1" title="{{ $user->email }}">{{ $user->email }}</p>
            <div class="flex items-center gap-2 mt-auto pt-2">
                @if($user->trashed())
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wide font-label">Deactivated</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wide font-label">Active</span>
                @endif
                <span class="text-xs text-stone-400 font-mono">{{ $user->created_at->format('M Y') }}</span>
            </div>
        </div>

        {{-- Listings --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5 flex items-center gap-4 hover:border-emerald-200 transition-colors">
            <div class="w-12 h-12 rounded-xl bg-stone-50 flex items-center justify-center text-stone-500 border border-stone-100">
                <span class="material-symbols-outlined text-xl">inventory_2</span>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-0.5 font-label">Listings</p>
                <p class="font-headline text-2xl font-extrabold text-emerald-950 leading-none">{{ $user->items->count() }}</p>
            </div>
        </div>

        {{-- Orders --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5 flex items-center gap-4 hover:border-emerald-200 transition-colors">
            <div class="w-12 h-12 rounded-xl bg-stone-50 flex items-center justify-center text-stone-500 border border-stone-100">
                <span class="material-symbols-outlined text-xl">shopping_bag</span>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-0.5 font-label">Purchases</p>
                <p class="font-headline text-2xl font-extrabold text-emerald-950 leading-none">{{ $user->buyerOrders->count() }}</p>
            </div>
        </div>

        {{-- CO2 Saved --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5 flex items-center gap-4 hover:border-emerald-200 transition-colors">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                <span class="material-symbols-outlined text-xl">eco</span>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-0.5 font-label">CO₂ Saved</p>
                <p class="font-mono text-xl font-bold text-emerald-700 leading-none">{{ number_format($user->total_co2_saved, 1) }}<span class="text-xs text-emerald-600/70 ml-0.5">kg</span></p>
            </div>
        </div>

    </div>

</div>
@endsection
