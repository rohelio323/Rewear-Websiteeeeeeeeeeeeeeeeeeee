<div class="group relative rounded-2xl overflow-hidden bg-white border border-stone-200 hover:-translate-y-0.5 transition-transform duration-200 cursor-pointer flex flex-col">  
    {{-- Image --}}
    <div class="relative" style="aspect-ratio:3/4;">
        @if($item->first_photo)
            <img src="{{ asset('storage/'.$item->first_photo) }}" alt="{{ $item->item_name }}" class="w-full h-full object-cover">
        @else
            <img src="/placeholder.jpg" alt="{{ $item->item_name }}" class="w-full h-full object-cover">
        @endif

        {{-- CO2 badge --}}
        <div class="absolute top-2.5 left-2.5 z-10 flex items-center gap-1.5 bg-emerald-950 text-emerald-300 text-[11px] font-medium px-3 py-1 rounded-full tracking-wide">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
            SAVED {{ (float) $item->category?->co2_constant ?? '0' }}KG CO2
        </div>

        {{-- Wishlist Button --}}
        @auth
            @php $isFavorited = auth()->user()->favorites->contains($item->id); @endphp
            <button
                class="wishlist-btn absolute top-2.5 right-2.5 z-10 flex items-center justify-center w-8 h-8 rounded-full bg-white/90 shadow-sm hover:scale-110 transition-all duration-150"
                data-url="{{ route('favorites.toggle', $item->id) }}"
                data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
                title="{{ $isFavorited ? 'Hapus dari wishlist' : 'Tambah ke wishlist' }}"
            >
                <span class="material-symbols-outlined text-base transition-all"
                    style="font-variation-settings:'FILL' {{ $isFavorited ? 1 : 0 }}; color: {{ $isFavorited ? '#ef4444' : '#a8a29e' }};">
                    favorite
                </span>
            </button>
        @else
            <a href="{{ route('login') }}" class="absolute top-2.5 right-2.5 z-10 flex items-center justify-center w-8 h-8 rounded-full bg-white/90 shadow-sm">
                <span class="material-symbols-outlined text-base text-stone-400">favorite</span>
            </a>
        @endauth
    </div>

    {{-- Body --}}
    <div class="px-3.5 py-3 flex flex-col flex-1">
        <div class="flex items-baseline justify-between gap-2 mb-1">
            <a href="{{ route('items.show', $item) }}" class="text-sm font-medium text-stone-900 leading-snug hover:text-emerald-900 transition-colors line-clamp-2 after:absolute after:inset-0 after:z-0">
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
            <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 self-start mt-auto">New With Tags</span>
        @elseif($item->condition === 'like_new')
            <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 self-start mt-auto">Like New</span>
        @else
            <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-stone-100 text-stone-500 self-start mt-auto">{{ str_replace('_', ' ', $item->condition) }}</span>
        @endif

        {{-- Buy Now / View Order --}}
        @auth
            <div class="relative z-10 mt-auto">
                @php
                    $existingOrder = \App\Models\Order::where('buyer_id', auth()->id())
                        ->where('item_id', $item->id)
                        ->where('status', 'pending')
                        ->first();
                @endphp

                @if($existingOrder)
                    <a href="{{ route('orders.show', $existingOrder) }}" class="mt-3 flex items-center justify-center w-full bg-stone-700 hover:bg-stone-600 text-white text-xs font-medium py-2 rounded-full transition-colors duration-200">
                        View Order →
                    </a>
                @else
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <button type="submit" class="mt-3 flex items-center justify-center w-full bg-emerald-900 hover:bg-emerald-800 text-white text-xs font-medium py-2 rounded-full transition-colors duration-200">
                            Buy Now
                        </button>
                    </form>
                @endif
            </div>
        @endauth
    </div>
</div>

@auth
@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.wishlist-btn');
        if (!btn) return;

        e.preventDefault();
        const url = btn.dataset.url;
        const icon = btn.querySelector('.material-symbols-outlined');
        const isFavorited = btn.dataset.favorited === 'true';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.favorited) {
                icon.style.fontVariationSettings = "'FILL' 1";
                icon.style.color = '#ef4444';
                btn.dataset.favorited = 'true';
                btn.title = 'Hapus dari wishlist';
            } else {
                icon.style.fontVariationSettings = "'FILL' 0";
                icon.style.color = '#a8a29e';
                btn.dataset.favorited = 'false';
                btn.title = 'Tambah ke wishlist';
            }
        })
        .catch(() => alert('Terjadi kesalahan.'));
    });
});
</script>
@endpush
@endonce
@endauth