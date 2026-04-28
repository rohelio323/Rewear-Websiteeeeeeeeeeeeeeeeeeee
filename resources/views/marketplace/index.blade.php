@extends('layouts.app')
@section('content')
<section>
    @if($items->isEmpty())
        <div style="text-align:center;padding:4rem;color:var(--color-text-muted);">
            <p style="font-size:3rem;">🧺</p>
            <p style="font-size:1.0625rem;margin-top:1rem;">No items match your filters.</p>
            <a href="{{ route('marketplace.index') }}" class="btn btn-secondary" style="margin-top:1rem;">Clear filters</a>
        </div>
        
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.125rem;">
            @foreach($items as $i => $item)
                <x-item-card :item="$item" />
            @endforeach
        </div>

       @auth
        @if(auth()->user()->is_verified_seller)
            <a href="{{ route('items.create') }}" 
            class="group fixed bottom-8 right-8 flex h-14 min-w-[3.5rem] items-center justify-center rounded-2xl bg-[#173124] px-4 text-[#ffffff] shadow-lg transition-all duration-300 ease-out hover:w-auto hover:rounded-xl hover:bg-[#324c3e] hover:shadow-2xl active:scale-95 focus:outline-none focus:ring-2 focus:ring-[#173124] focus:ring-offset-2 z-50">
                
                <div class="flex items-center justify-center transition-transform duration-500 group-hover:rotate-90 group-hover:text-[#b0cdbb]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>

                <span class="max-w-0 overflow-hidden whitespace-nowrap text-sm font-bold uppercase tracking-widest opacity-0 transition-all duration-300 ease-in-out group-hover:ml-3 group-hover:max-w-xs group-hover:opacity-100">
                    Create Listing
                </span>
            </a>
        @endif
    @endauth
        
    @endif
    
</section>
@endsection