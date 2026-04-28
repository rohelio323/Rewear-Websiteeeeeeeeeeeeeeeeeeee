@extends('layouts.app')
@section('title', 'Account Settings')

@section('content')
<div class="py-12 max-w-5xl mx-auto w-full">
    
    <div class="mb-8 border-b border-stone-200 pb-4">
        <h1 class="text-3xl font-extrabold tracking-tight text-emerald-900 font-headline mb-1">Account Settings</h1>
        <p class="text-stone-500 text-sm">Manage your personal details, security, and view your ReWear impact.</p>
    </div>

    <div class="space-y-8">
        
        <div class="p-8 bg-emerald-900 shadow-xl shadow-emerald-900/10 rounded-3xl text-emerald-50 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="absolute -top-10 -right-10 opacity-10 pointer-events-none">
                <span class="material-symbols-outlined text-[200px] text-emerald-300">eco</span>
            </div>
            
            <div class="relative z-10 max-w-lg">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-emerald-400">psychiatry</span>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-300">
                        Lifetime Impact
                    </h3>
                </div>
                <p class="text-sm text-emerald-100/90 leading-relaxed">
                    Thank you for championing the circular economy. This is the total CO₂ saved across all your pre-loved purchases on ReWear.
                </p>
            </div>
            
            <div class="relative z-10 flex flex-col items-end shrink-0 bg-emerald-950/50 px-8 py-5 rounded-2xl border border-emerald-800/50">
                <div class="flex items-baseline gap-1">
                    <span class="text-5xl font-black text-white tracking-tighter">{{ number_format($totalCo2Saved, 2) }}</span>
                    <span class="text-lg font-bold text-emerald-400">kg</span>
                </div>
                <span class="text-xs text-emerald-300 font-medium uppercase tracking-wider mt-1">CO₂ Saved</span>
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