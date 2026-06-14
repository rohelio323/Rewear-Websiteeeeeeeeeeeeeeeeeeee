@extends('layouts.app')
@section('title', 'Account Settings & Impact')

@section('content')
{{-- Fetch the absolute latest database record to prevent stale session data --}}
@php
    $currentUser = auth()->user()->fresh();
@endphp

<main class="bg-[#fafaf9] min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <header class="mb-10 border-b border-stone-200 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-emerald-950 font-headline mb-2">Account Settings</h1>
                <p class="text-stone-500 text-sm">Manage your personal details, security, and view your ReWear impact.</p>
            </div>

            {{-- Quick Profile Badge --}}
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-full border border-stone-200 shadow-sm w-max">
                <div class="w-8 h-8 bg-emerald-100 text-emerald-800 rounded-full flex items-center justify-center font-bold text-xs">
                    {{ substr($currentUser->name ?? 'U', 0, 1) }}
                </div>
                <div class="text-sm">
                    <p class="font-bold text-emerald-950 leading-none">{{ $currentUser->name }}</p>
                    <p class="text-[10px] text-stone-400 mt-0.5">{{ $currentUser->email }}</p>
                </div>
            </div>
        </header>

        {{-- Warning Notification --}}
        @if($currentUser->warning_count > 0)
            <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-3 rounded-2xl text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600 text-base">warning</span>
                Your account has <strong class="mx-1">{{ $currentUser->warning_count }} warning{{ $currentUser->warning_count > 1 ? 's' : '' }}</strong> for violating community standards. Please review our guidelines.
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- LEFT COLUMN: The Trophy Case (Sticky Sidebar) --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="sticky top-8 space-y-6">

                    {{-- Unified Impact Card --}}
                    <div class="bg-emerald-950 rounded-3xl p-6 text-emerald-50 relative overflow-hidden shadow-lg border border-emerald-900">
                        <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
                            <span class="material-symbols-outlined text-[150px] text-emerald-300">eco</span>
                        </div>

                        <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-400 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">psychiatry</span>
                            Lifetime Impact
                        </h3>

                        <div class="space-y-6 relative z-10">
                            {{-- CO2 Stat (Now fully synchronized) --}}
                            <div>
                                <span class="text-xs text-emerald-300/80 uppercase tracking-wider block mb-1">CO₂ Saved</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-black text-white tracking-tighter">{{ number_format($currentUser->total_co2_saved, 2) }}</span>
                                    <span class="text-sm font-bold text-emerald-400">kg</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-emerald-800/50">
                                {{-- Rank Stat --}}
                                <div>
                                    <span class="text-[10px] text-emerald-300/80 uppercase tracking-wider block mb-1">Rank</span>
                                    <span class="text-2xl font-bold text-white">#{{ $myRank ?? '-' }}</span>
                                </div>
                                {{-- Score Stat --}}
                                <div>
                                    <span class="text-[10px] text-emerald-300/80 uppercase tracking-wider block mb-1">Points</span>
                                    <span class="text-2xl font-bold text-white">{{ $myScore ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Seller Status Card --}}
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-emerald-600 text-xl">storefront</span>
                            <h2 class="text-lg font-bold text-emerald-900">Seller Hub</h2>
                        </div>

                        @if($currentUser->isVerifiedSeller())
                            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-center gap-3">
                                <span class="material-symbols-outlined text-emerald-600 text-2xl">verified</span>
                                <div>
                                    <p class="font-bold text-emerald-800 text-sm">Verified Seller</p>
                                    <p class="text-xs text-emerald-600 mt-0.5">Your store is active.</p>
                                </div>
                            </div>
                        @elseif($currentUser->hasPendingSellerRequest())
                            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 flex items-center gap-3">
                                <span class="material-symbols-outlined text-amber-600 text-2xl">schedule</span>
                                <div>
                                    <p class="font-bold text-amber-800 text-sm">Application Pending</p>
                                    <p class="text-[10px] text-amber-600 uppercase tracking-widest mt-0.5">Submitted {{ $currentUser->seller_requested_at?->diffForHumans() }}</p>
                                </div>
                            </div>
                        @elseif($currentUser->wasRejectedAsSeller())
                            <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-red-600 text-sm">cancel</span>
                                    <p class="font-bold text-red-800 text-sm">Application Rejected</p>
                                </div>
                                @if($currentUser->seller_rejection_note)
                                    <p class="text-xs text-red-600/80 mb-3 bg-white/50 p-2 rounded-lg">{{ $currentUser->seller_rejection_note }}</p>
                                @endif
                                <form method="POST" action="{{ route('seller.apply.submit') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-center px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition">Reapply</button>
                                </form>
                            </div>
                        @else
                            <p class="text-sm text-stone-500 mb-4 leading-relaxed">Ready to clear out your closet? List your pre-loved items on ReWear.</p>
                            <form method="POST" action="{{ route('seller.apply.submit') }}">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-900 text-white text-sm font-bold hover:bg-emerald-800 transition shadow-sm">
                                    <span class="material-symbols-outlined text-sm">add_business</span>
                                    Become a Seller
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200">
                        @include('profile.partials.voucher-exchange')
                    </div>
                </div>
            </div>

            

            {{-- RIGHT COLUMN: The Control Room (Forms & History) --}}
            <div class="lg:col-span-8 space-y-8">

                {{-- Challenge History --}}
                <section class="bg-white shadow-sm rounded-3xl border border-stone-200 overflow-hidden">
                    <div class="p-6 sm:p-8 border-b border-stone-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-emerald-900 mb-1">Challenge History</h2>
                            <p class="text-sm text-stone-500">Your documented sustainable stories.</p>
                        </div>
                        <span class="bg-stone-100 text-stone-600 text-xs font-bold px-3 py-1 rounded-full">{{ $totalPosts ?? 0 }} Posts</span>
                    </div>

                    <div class="p-6 sm:p-8 bg-stone-50/50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(isset($challengeHistory) && $challengeHistory->isNotEmpty())
                                @foreach($challengeHistory as $post)
                                    <div class="bg-white border border-stone-200 rounded-2xl p-4 flex gap-4 items-center hover:shadow-md transition duration-300">
                                        @if($post->image_path)
                                            <img src="{{ asset('storage/' . $post->image_path) }}" class="w-16 h-16 object-cover rounded-xl shrink-0">
                                        @else
                                            <div class="w-16 h-16 bg-emerald-50 rounded-xl shrink-0 flex items-center justify-center border border-emerald-100">
                                                <span class="text-xl">👕</span>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-sm text-stone-800 truncate">{{ $post->title }}</h4>
                                            <p class="text-[10px] text-stone-400 font-medium uppercase tracking-widest mb-1.5">{{ $post->created_at->format('M d, Y') }}</p>
                                            <div class="flex flex-wrap gap-1">
                                                @php
                                                    $tagsArray = is_array($post->tags) ? $post->tags : (json_decode($post->tags, true) ?? explode(',', $post->tags));
                                                @endphp
                                                @foreach($tagsArray as $tag)
                                                    @if(trim($tag) !== '')
                                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                                                            #{{ trim($tag) }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-10">
                                    <span class="material-symbols-outlined text-4xl text-stone-300 mb-2">history</span>
                                    <h3 class="font-bold text-stone-700 text-sm">No stories yet</h3>
                                    <p class="text-stone-400 text-xs mt-1">Head to the Living Archive to join a challenge!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- Administrative Forms Container --}}
                <div class="grid grid-cols-1 gap-8">
                    {{-- Profile Information Form --}}
                    <section class="bg-white shadow-sm rounded-3xl border border-stone-200 p-6 sm:p-8">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </section>

                    {{-- Update Password Form --}}
                    <section class="bg-white shadow-sm rounded-3xl border border-stone-200 p-6 sm:p-8">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </section>

                    {{-- Delete Account Form --}}
                    <section class="bg-red-50/30 shadow-sm rounded-3xl border border-red-100 p-6 sm:p-8">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>
</main>
@endsection
