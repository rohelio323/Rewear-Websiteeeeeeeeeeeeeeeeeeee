@extends('layouts.admin')
@section('title', 'User — '.$user->name)

@section('content')
<div class="max-w-5xl mx-auto font-body">

    {{-- Back Link --}}
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-stone-500 hover:text-emerald-700 transition-colors mb-8 group">
        <span class="material-symbols-outlined text-sm transition-transform group-hover:-translate-x-1">arrow_back</span>
        Back to Users
    </a>

    {{-- User Identity & Status Card --}}
    <div class="bg-white rounded-3xl border border-stone-200 p-6 md:p-8 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm">
        
        <div class="flex items-center gap-6">
            {{-- Avatar (Replaced inline styles with Tailwind shrink-0, w-16, h-16) --}}
            <div class="w-16 h-16 shrink-0 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-900 text-2xl font-bold shadow-inner">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover rounded-full" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>

            {{-- Core Info --}}
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-2xl font-semibold text-stone-900 tracking-tight leading-none">{{ $user->name }}</h1>
                    @if($user->is_verified_seller)
                        <span class="material-symbols-outlined text-emerald-600 text-[22px]" title="Verified Seller">verified</span>
                    @endif
                </div>
                <div class="flex items-center gap-3 text-sm text-stone-500">
                    <span class="font-medium text-stone-600">{{ $user->email }}</span>
                    <span class="w-1 h-1 rounded-full bg-stone-300"></span>
                    <span class="font-mono text-xs">Joined {{ $user->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Badges / Status --}}
        <div class="flex flex-wrap items-center gap-2">
            @if($user->role === 'admin')
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold uppercase tracking-widest font-label border border-purple-100/50">Admin</span>
            @endif

            @if($user->trashed())
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold uppercase tracking-widest font-label border border-red-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Deactivated
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-widest font-label border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                </span>
            @endif
        </div>
    </div>

    {{-- Metrics Grid (Now 3 columns, exclusively for numbers) --}}
    <h2 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-4 font-label px-2">Platform Activity</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        {{-- Listings --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 flex flex-col justify-between hover:border-emerald-200 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold uppercase tracking-widest text-stone-400 font-label">Active Listings</p>
                <div class="w-10 h-10 rounded-full bg-stone-50 flex items-center justify-center text-stone-500 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
            </div>
            <p class="font-headline text-4xl font-extrabold text-stone-900 leading-none">{{ $user->items->count() }}</p>
        </div>

        {{-- Orders --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 flex flex-col justify-between hover:border-emerald-200 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold uppercase tracking-widest text-stone-400 font-label">Total Purchases</p>
                <div class="w-10 h-10 rounded-full bg-stone-50 flex items-center justify-center text-stone-500 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                </div>
            </div>
            <p class="font-headline text-4xl font-extrabold text-stone-900 leading-none">{{ $user->buyerOrders->count() }}</p>
        </div>

        {{-- CO2 Saved (Highlighted for the sustainable mission) --}}
        <div class="bg-gradient-to-br from-emerald-50 to-[#f4f4f1] rounded-3xl border border-emerald-200/60 p-6 flex flex-col justify-between hover:shadow-md hover:border-emerald-300 transition-all shadow-[inset_0_0_20px_rgba(255,255,255,0.5)]">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-700 font-label">CO₂ Saved</p>
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">eco</span>
                </div>
            </div>
            <p class="font-mono text-4xl font-bold text-emerald-800 leading-none">
                {{ number_format($user->total_co2_saved, 1) }}<span class="text-lg text-emerald-600/70 ml-1 font-medium tracking-normal">kg</span>
            </p>
        </div>

    </div>

</div>
@endsection