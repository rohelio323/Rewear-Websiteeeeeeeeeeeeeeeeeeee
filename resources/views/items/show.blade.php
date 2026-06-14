@extends('layouts.app')

@section('content')
<main class="pt-5 pb-20 px-28 max-w-screen-2xl mx-auto">
    <div class="mb-6 flex justify-between">
        <a href="{{ route('marketplace.index') }}"
            class="group inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined transition-transform group-hover:-translate-x-1">arrow_back</span>
            Back to Marketplace
        </a>

        @auth
            @if(auth()->id() === $item->user->id)
                <div class="flex items-center gap-2">
                    <a href="{{ route('items.edit', $item) }}"
                        class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-surface-container-high text-on-surface-variant font-bold text-xs uppercase tracking-wider hover:brightness-95 transition">
                        <span class="material-symbols-outlined text-sm">edit</span> Edit
                    </a>

                    <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-error-container text-on-error-container font-bold text-xs uppercase tracking-wider hover:brightness-95 transition">
                            <span class="material-symbols-outlined text-sm">delete</span> Delete
                        </button>
                    </form>
                </div>
            @endif
        @endauth
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <div class="lg:col-span-6 space-y-6">

            <div class="rounded-xl overflow-hidden bg-surface-container-low aspect-[3/4] relative group max-w-2xl mx-auto">
                @if($item->first_photo)
                    <img id="main-image" src="{{ asset('storage/'.$item->first_photo) }}" alt="{{ $item->item_name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-stone-200">
                        <img src="/placeholder.jpg" alt="{{ $item->item_name }}" class="w-full h-full object-cover">
                    </div>
                @endif
            </div>

            @if(count($item->photo_path ?? []) > 1)
                <div class="grid grid-cols-5 gap-3 max-w-2xl mx-auto">
                    @foreach($item->photo_path as $index => $photo)
                        <div onclick="switchImage(this, '{{ asset('storage/' . $photo) }}')"
                            class="thumbnail rounded-lg overflow-hidden bg-surface-container-low aspect-square cursor-pointer hover:opacity-80 transition-opacity {{ $index === 0 ? 'border-2 border-primary' : '' }}">
                            <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover" alt="Detail {{ $index + 1 }}">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="lg:col-span-6 flex flex-col space-y-8">
            <header>
                <nav class="flex items-center gap-2 text-[11px] uppercase tracking-widest text-on-surface-variant mb-4 font-bold">
                    <a class="hover:text-primary transition-colors" href="#">Marketplace</a>
                    <span class="material-symbols-outlined text-[10px]">arrow_forward_ios</span>
                    <a class="hover:text-primary transition-colors" href="#">{{ $item->category->category_name ?? 'Uncategorized' }}</a>
                </nav>

                <h1 class="text-3xl lg:text-4xl font-extrabold font-headline tracking-tighter text-primary mb-2">
                    {{ $item->item_name }}
                </h1>

                <div class="flex items-baseline gap-4">
                    <span class="text-2xl font-bold font-headline text-primary">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                </div>
            </header>

            <!-- item info -->
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-lg bg-surface-container-low">
                    <span class="block text-[10px] text-on-surface-variant uppercase tracking-widest font-bold mb-1">Condition</span>
                    <span class="text-sm font-bold text-primary uppercase">{{ str_replace('_', ' ', $item->condition) }}</span>
                </div>
                <div class="p-4 rounded-lg bg-surface-container-low">
                    <span class="block text-[10px] text-on-surface-variant uppercase tracking-widest font-bold mb-1">Size</span>
                    <span class="text-sm font-bold text-primary uppercase">{{ $item->size }}</span>
                </div>
            </div>

            <div class="space-y-3">
                <h3 class="text-md font-bold font-headline text-primary uppercase tracking-tight">ITEM DESCRIPTION</h3>
                <p class="text-on-surface-variant leading-relaxed text-sm">
                    {{ $item->description }}
                </p>
            </div>

            <div class="p-5 rounded-xl bg-surface-container flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-secondary-fixed relative ring-2 ring-white">
                        <img class="w-full h-full object-cover"
                             src="{{ $item->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($item->user->name) }}"
                             alt="{{ $item->user->name }}">
                    </div>
                    <div>
                        {{-- Seller name now clickable → opens seller profile in new tab --}}
                        <a href="{{ route('seller.profile', $item->user) }}" target="_blank"
                           class="font-bold text-sm text-primary flex items-center gap-1 hover:underline">
                            {{ $item->user->name }}
                            <span class="material-symbols-outlined text-emerald-600 text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </a>
                        <div class="flex items-center text-[11px] text-on-surface-variant gap-2">
                            <span class="flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[12px]">location_on</span> {{ $item->city ?? 'Indonesia' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- PBI-39: Seller Rating Badge --}}
                @php
                    $avgRating = $item->user->averageRating();
                    $totalReviews = $item->user->totalReviews();
                @endphp
                <div class="flex items-center gap-1.5">
                    @if($totalReviews > 0)
                        <span class="material-symbols-outlined text-amber-500 text-base" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="text-sm font-bold text-primary">{{ $avgRating }}</span>
                        <span class="text-xs text-on-surface-variant">({{ $totalReviews }})</span>
                    @else
                        <span class="text-xs text-on-surface-variant italic">No reviews yet</span>
                    @endif
                </div>
            </div>

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

                @if(auth()->id() !== $item->users_id)
                    <button onclick="document.getElementById('itemReportModal').classList.remove('hidden')"
                        class="mt-3 w-full text-center text-xs text-stone-400 hover:text-red-500 transition py-1">
                        🚩 Report this listing
                    </button>
                @endif
            </div>
            @endauth
        </div>
    </div>

    {{-- PBI-29: Item Report Modal --}}
    @auth
    <div id="itemReportModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative">
            <button onclick="document.getElementById('itemReportModal').classList.add('hidden')"
                class="absolute top-5 right-6 text-stone-400 hover:text-red-500 text-xl font-bold">✕</button>
            <h2 class="text-xl font-bold mb-5 text-red-700">🚩 Report Listing</h2>
            <form action="{{ route('reports.store') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <input type="hidden" name="reportable_type" value="item">
                <input type="hidden" name="reportable_id" value="{{ $item->id }}">
                <div>
                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-widest mb-2">Reason *</label>
                    <textarea name="reason" rows="4" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-400 focus:outline-none resize-none"
                        placeholder="Describe why you're reporting this listing..."></textarea>
                </div>
                <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-full text-sm font-bold hover:bg-red-700 transition w-full">
                    Submit Report
                </button>
            </form>
        </div>
    </div>
    @endauth

    {{-- Similar Items --}}
    @if($similarItems->count() > 0)
        <section class="mt-24">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <span class="text-secondary font-bold text-[11px] tracking-[0.2em] uppercase mb-1 block">Curated For You</span>
                    <h2 class="text-2xl font-black font-headline tracking-tighter text-primary">Similar Stories</h2>
                </div>
                <a class="text-primary font-bold flex items-center gap-2 decoration-2 underline-offset-4" href="#" flex>
                    <span class="hover:underline text-xs">See All</span>
                    <span class="material-symbols-outlined text-sm">arrow_right_alt</span>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @foreach($similarItems as $similar)
                    <x-item-card :item="$similar" />
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection

@push('scripts')
<script>
    function switchImage(thumbnail, src) {
        document.getElementById('main-image').src = src;
        document.querySelectorAll('.thumbnail').forEach(t => {
            t.classList.remove('border-2', 'border-primary');
        });
        thumbnail.classList.add('border-2', 'border-primary');
    }
</script>
@endpush