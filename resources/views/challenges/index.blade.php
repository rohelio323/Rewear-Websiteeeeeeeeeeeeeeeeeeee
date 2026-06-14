@extends('layouts.app')
@section('title', 'Community Challenges')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 font-body">
    
    {{-- Header --}}
    <div class="text-center max-w-2xl mx-auto mb-16">
        <p class="text-[11px] font-bold uppercase tracking-widest text-emerald-600 mb-2 font-label">Community</p>
        <h1 class="text-4xl font-extrabold text-stone-900 tracking-tight font-headline mb-4">ReWear Challenges</h1>
        <p class="text-lg text-stone-500">Join weekly sustainability events, upcycle your wardrobe, and earn exclusive eco-badges.</p>
    </div>

    {{-- Challenge Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($activeChallenges as $challenge)
            <div class="bg-white rounded-3xl border border-stone-200 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full">
                
                {{-- Decorative Top Banner --}}
                <div class="h-32 bg-emerald-900 relative overflow-hidden flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-800/50 text-[120px] absolute -right-4 -bottom-4 transform rotate-12 group-hover:scale-110 transition-transform duration-500">eco</span>
                    <div class="absolute bottom-4 left-6">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-100 border border-emerald-400/30 text-[10px] font-bold uppercase tracking-wider backdrop-blur-sm shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live Now
                        </span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-6 flex-grow flex flex-col">
                    <h3 class="text-xl font-extrabold text-stone-900 font-headline mb-2">{{ $challenge->title }}</h3>
                    <p class="text-sm text-stone-600 mb-4 flex-grow">{{ Str::limit($challenge->description, 120) }}</p>
                    
                    {{-- NEW: Compact Reward Badge --}}
                    @if($challenge->reward_points > 0)
                        <div class="mb-5 flex items-center gap-2 p-2.5 rounded-xl bg-amber-50 border border-amber-100 shadow-sm self-start">
                            <span class="material-symbols-outlined text-[18px] text-amber-500">stars</span>
                            <span class="text-xs font-extrabold text-amber-900">
                                +{{ $challenge->reward_points }} CO₂ Saved
                            </span>
                        </div>
                    @endif
                    
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-stone-100">
                        <div class="flex items-center gap-2 text-xs font-bold text-stone-500 font-mono">
                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                            Ends {{ \Carbon\Carbon::parse($challenge->end_date)->format('M d') }}
                        </div>
                        
                        <a href="{{ route('challenges.show', $challenge->id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-stone-100 hover:bg-emerald-50 text-stone-700 hover:text-emerald-700 rounded-xl text-sm font-bold transition-colors">
                            View Details
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-3xl border border-stone-200 border-dashed">
                <div class="w-20 h-20 mx-auto bg-stone-50 rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-4xl text-stone-300">notifications_paused</span>
                </div>
                <h3 class="text-lg font-bold text-stone-900 font-headline mb-2">No Active Challenges</h3>
                <p class="text-stone-500 text-sm">Our team is brewing up the next big sustainability event. Check back soon!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection