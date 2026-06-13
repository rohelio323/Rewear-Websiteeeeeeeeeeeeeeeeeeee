@extends('layouts.app')
@section('title', 'Account Settings')

@section('content')
<div class="py-12 max-w-5xl mx-auto w-full">
    <div class="mb-8 border-b border-stone-200 pb-4">
        <h1 class="text-3xl font-extrabold tracking-tight text-emerald-900 font-headline mb-1">Account Settings</h1>
        <p class="text-stone-500 text-sm">Manage your personal details, security, and view your ReWear impact.</p>
    </div>

<div class="space-y-8">

        {{-- Warning Notification --}}
        @if(auth()->user()->warning_count > 0)
            <div class="bg-amber-50 border border-amber-200 text-amber-800 px-5 py-3 rounded-2xl text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600 text-base">warning</span>
                Your account has <strong class="mx-1">{{ auth()->user()->warning_count }} warning{{ auth()->user()->warning_count > 1 ? 's' : '' }}</strong> for violating community standards. Please review our guidelines.
            </div>
        @endif

        {{-- CO2 Impact Card --}}
        <div class="p-8 bg-emerald-900 shadow-xl shadow-emerald-900/10 rounded-3xl text-emerald-50 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="absolute -top-10 -right-10 opacity-10 pointer-events-none">
                <span class="material-symbols-outlined text-[200px] text-emerald-300">eco</span>
            </div>
            <div class="relative z-10 max-w-lg">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-emerald-400">psychiatry</span>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-300">Lifetime Impact</h3>
                </div>
                <p class="text-sm text-emerald-100/90 leading-relaxed">
                    Thank you for championing the circular economy. This is the total CO₂ saved across all your pre-loved purchases on ReWear.
                </p>
            </div>
            <div class="relative z-10 flex flex-col items-end shrink-0 bg-emerald-950/50 px-8 py-5 rounded-2xl border border-emerald-800/50">
                <div class="flex items-baseline gap-1">
                <span class="text-5xl font-black text-white tracking-tighter">{{ number_format(auth()->user()->total_co2_saved, 2) }}</span>
                    <span class="text-lg font-bold text-emerald-400">kg</span>
                </div>
                <span class="text-xs text-emerald-300 font-medium uppercase tracking-wider mt-1">CO₂ Saved</span>
            </div>
        </div>

        {{-- Seller Status Card --}}
        <div class="p-6 sm:p-8 bg-white shadow-sm rounded-3xl border border-stone-200">
            @if(session('status') === 'seller-applied')
                <p class="text-sm text-emerald-600 font-medium">Your application has been submitted!</p>
            @endif
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-emerald-600">storefront</span>
                        <h2 class="text-lg font-bold text-emerald-900">Seller Account</h2>
                    </div>
                    <p class="text-sm text-stone-500">List your pre-loved items and start selling on ReWear.</p>
                </div>

                {{-- Verified Seller --}}
                @if(auth()->user()->isVerifiedSeller())
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-100 text-emerald-800 text-sm font-semibold">
                        <span class="material-symbols-outlined text-base">verified</span>
                        Verified Seller
                    </span>

                {{-- Pending --}}
                @elseif(auth()->user()->hasPendingSellerRequest())
                    <div class="flex flex-col items-end gap-1">
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold">
                            <span class="material-symbols-outlined text-base">schedule</span>
                            Application Pending
                        </span>
                        <p class="text-xs text-stone-400">Submitted {{ auth()->user()->seller_requested_at?->diffForHumans() }}</p>
                    </div>

                {{-- Rejected --}}
                @elseif(auth()->user()->wasRejectedAsSeller())
                    <div class="flex flex-col items-end gap-2">
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-red-100 text-red-700 text-sm font-semibold">
                            <span class="material-symbols-outlined text-base">cancel</span>
                            Application Rejected
                        </span>
                        @if(auth()->user()->seller_rejection_note)
                            <p class="text-xs text-stone-500 max-w-xs text-right">
                                <span class="font-medium text-stone-600">Reason:</span> {{ auth()->user()->seller_rejection_note }}
                            </p>
                        @endif
                        <form method="POST" action="{{ route('seller.apply.submit') }}">
                            @csrf
                            <button type="submit"
                                    class="mt-1 inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-900 text-white text-sm font-semibold hover:bg-emerald-800 transition">
                                <span class="material-symbols-outlined text-base">refresh</span>
                                Reapply
                            </button>
                        </form>
                    </div>

                {{-- Not applied yet --}}
                @else
                    <form method="POST" action="{{ route('seller.apply.submit') }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full bg-emerald-900 text-white text-sm font-semibold hover:bg-emerald-800 transition shadow-sm">
                            <span class="material-symbols-outlined text-base">add_business</span>
                            Become a Seller
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="p-6 sm:p-8 bg-white shadow-sm rounded-3xl border border-stone-200 md:col-span-2">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white shadow-sm rounded-3xl border border-stone-200 h-full">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-red-50/50 shadow-sm rounded-3xl border border-red-100 h-full">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
