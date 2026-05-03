@extends('layouts.app')
@section('content')
<section>
    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-secondary mb-2">Your Collection</p>
            <h1 class="font-headline font-bold text-4xl text-primary leading-tight">Saved Items</h1>
            <p class="text-stone-400 text-sm mt-2">{{ $items->total() }} item tersimpan</p>
        </div>
        <a href="{{ route('marketplace.index') }}"
           class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-stone-500 hover:text-primary border border-stone-200 px-4 py-2 rounded-full transition-all hover:border-primary mt-2">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Marketplace
        </a>
    </div>

    {{-- Empty State --}}
    @if($items->isEmpty())
        <div style="text-align:center;padding:4rem;">
            <p style="font-size:3rem;">🤍</p>
            <p style="font-size:1.0625rem;margin-top:1rem;" class="text-stone-400">Your wishlist is empty.</p>
            <a href="{{ route('marketplace.index') }}" class="inline-block mt-4 bg-primary text-white text-xs font-bold uppercase tracking-widest px-6 py-2.5 rounded-full hover:opacity-90 transition-all">
                Explore Marketplace
            </a>
        </div>

    {{-- Item Grid --}}
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($items as $item)
                <x-item-card :item="$item" />
            @endforeach
        </div>
    @endif

</section>
@endsection