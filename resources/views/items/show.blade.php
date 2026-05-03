@extends('layouts.app')

@section('content')
<main class="pt-5 pb-20 px-28 max-w-screen-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('marketplace.index') }}" 
            class="group inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined transition-transform group-hover:-translate-x-1">arrow_back</span>
            Back to Marketplace
        </a>
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
                        <div class="font-bold text-sm text-primary flex items-center gap-1">
                            {{ $item->user->name }}
                            <span class="material-symbols-outlined text-emerald-600 text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <div class="flex items-center text-[11px] text-on-surface-variant gap-2">
                            
                            <span class="flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[12px]">location_on</span> {{ $item->city ?? 'Indonesia' }}
                            </span>
                        </div>
                    </div>
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
            </div>
            @endauth
        </div>
    </div>

    {{-- Similar Items (Smaller Images) --}}
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