@extends('layouts.admin')
@section('title', 'Edit Challenge')

@section('content')
<main class="bg-[#fafaf9] min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        
        <header class="mb-8 flex items-center gap-4">
            <a href="{{ route('admin.challenges.index') }}" class="w-10 h-10 bg-white border border-stone-200 rounded-full flex items-center justify-center text-stone-500 hover:bg-stone-50 transition shadow-sm">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-emerald-950 font-headline mb-1">Edit Challenge</h1>
                <p class="text-stone-500 text-sm">Update the details for "{{ $challenge->title }}".</p>
            </div>
        </header>

        <section class="bg-white shadow-sm rounded-3xl border border-stone-200 p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.challenges.update', $challenge->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">Challenge Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $challenge->title) }}" required
                           class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 font-medium focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm placeholder-stone-400">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Hashtag --}}
                <div>
                    <label for="hashtag" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-1">Challenge Hashtag <span class="text-red-500">*</span></label>
                    <p class="text-xs text-stone-400 mb-2 leading-relaxed">Users enter by tagging posts with this (no # needed here). Must be unique, no spaces.</p>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-stone-400 font-bold sm:text-sm">#</span>
                        </div>
                        <input type="text" id="hashtag" name="hashtag" value="{{ old('hashtag', $challenge->hashtag) }}" required 
                               class="w-full pl-8 pr-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent bg-stone-50 focus:bg-white transition-colors text-stone-800 font-medium" 
                               placeholder="rewear30days">
                    </div>
                    @error('hashtag')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">Description</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm placeholder-stone-400">{{ old('description', $challenge->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Rewards --}}
                <div>
                    <label for="reward_points" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">Reward (CO₂ Points)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600 text-[18px]">eco</span>
                        <input type="number" id="reward_points" name="reward_points" value="{{ old('reward_points', $challenge->reward_points ?? 0) }}" min="0" required
                               class="w-full pl-11 pr-4 py-3 bg-emerald-50/50 border border-emerald-200 rounded-xl text-sm text-emerald-900 font-mono font-bold focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm">
                    </div>
                    <p class="text-xs text-stone-400 mt-2">These CO₂ points will be added to the winner's total impact score.</p>
                    @error('reward_points')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Start Date --}}
                    <div>
                        <label for="start_date" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($challenge->start_date)->format('Y-m-d')) }}" required
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm">
                        @error('start_date')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label for="end_date" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date', \Carbon\Carbon::parse($challenge->end_date)->format('Y-m-d')) }}" required
                               class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm">
                        @error('end_date')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Active Toggle --}}
                <div class="flex items-center gap-3 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $challenge->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 text-emerald-600 bg-white border-emerald-300 rounded focus:ring-emerald-600 focus:ring-2">
                    <label for="is_active" class="text-sm font-bold text-emerald-900 cursor-pointer flex-1">Challenge is currently active</label>
                </div>

                {{-- Submit --}}
                <div class="pt-4 border-t border-stone-100 flex justify-end">
                    <button type="submit" class="bg-emerald-900 text-white px-8 py-3 rounded-full text-sm font-bold hover:bg-emerald-800 transition shadow-sm active:scale-95">
                        Save Changes
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>
@endsection