<div class="rounded-2xl overflow-hidden bg-white border border-stone-200 hover:-translate-y-0.5 transition-transform duration-200 cursor-pointer">
    {{-- Image --}}
    <div class="relative" style="aspect-ratio:3/4;">
        @if($item->first_photo)
            <img src="{{ asset('storage/'.$item->first_photo) }}"
                 alt="{{ $item->item_name }}"
                 class="w-full h-full object-cover">
        @else
            <img src="/placeholder.jpg"
                 alt="{{ $item->item_name }}"
                 class="w-full h-full object-cover">
        @endif

        {{-- CO2 badge --}}
        <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 bg-emerald-950 text-emerald-300 text-[11px] font-medium px-3 py-1 rounded-full tracking-wide">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
            SAVED {{ (float) $item->category?->co2_constant ?? '0' }}KG CO2
        </div>
        @auth
            <form action="{{ route('favorites.toggle', $item->id) }}" method="POST" class="absolute top-2.5 right-2.5 z-10">
                @csrf
                <button type="submit" 
                    class="wishlist-btn flex items-center justify-center w-8 h-8 rounded-full bg-white/90 shadow-sm transition-transform active:scale-90"
                    title="{{ auth()->user()->favorites->contains($item->id) ? 'Hapus' : 'Tambah' }}">
                    
                    <span class="material-symbols-outlined text-base {{ auth()->user()->favorites->contains($item->id) ? 'text-red-500' : 'text-stone-400' }}" 
                        style="font-variation-settings:'FILL' {{ auth()->user()->favorites->contains($item->id) ? 1 : 0 }};">
                        favorite
                    </span>
                </button>
            </form>
        @else
            {{-- For guests, just a link to login --}}
            <a href="{{ route('login') }}" class="absolute top-2.5 right-2.5 z-10 flex items-center justify-center w-8 h-8 rounded-full bg-white/90 shadow-sm">
                <span class="material-symbols-outlined text-base text-stone-400">favorite</span>
            </a>
        @endauth
    </div>

    {{-- Body --}}
    <div class="px-3.5 py-3">
        <div class="flex items-baseline justify-between gap-2 mb-1">
            <a href="#"
               class="text-sm font-medium text-stone-900 leading-snug hover:text-emerald-900 transition-colors line-clamp-2">
                {{ $item->item_name }}
            </a>
            <span class="text-sm font-medium text-stone-900 whitespace-nowrap">
                Rp {{ number_format($item->price, 0, ',', '.') }}
            </span>
        </div>

        <p class="text-xs text-stone-400 mb-2">
            {{ $item->user?->city ?? 'Indonesia' }} &middot; Size {{ $item->size ?? '—' }}
        </p>

        {{-- Condition tag --}}
        @if($item->condition === 'new_with_tags')
            <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-orange-100 text-orange-800">New With Tags</span>
        @elseif($item->condition === 'like_new')
            <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Like New</span>
        @else
            <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-stone-100 text-stone-500">{{ str_replace('_', ' ', $item->condition) }}</span>
        @endif

        {{-- Buy Now --}}
        @auth
        <form action="{{ route('orders.store') }}" method="POST">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <button type="submit"
                class="mt-3 flex items-center justify-center w-full bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-medium py-2 rounded-full transition-colors duration-200">
                Buy Now
            </button>
        </form>
        @endauth
    </div>
</div>