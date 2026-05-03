@extends('layouts.app')
@section('title', 'Order Review #'.$order->id)

@section('content')
<main class="pt-10 pb-24 px-4 max-w-[900px] mx-auto min-h-screen">
    {{-- Header --}}
    <div class="mb-8">
        <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400 mb-1">Order #{{ $order->id }}</p>
        <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 mb-2">Order Review</h1>
        <p class="text-stone-500 font-medium">Verify your selection and contribution to the circular economy.</p>
    </div>

    <div class="flex flex-col gap-8">
        <div class="bg-white p-6 md:p-8 rounded-2xl border border-stone-200 shadow-sm">
            <h3 class="text-xl font-bold text-emerald-900 mb-6">Order Status</h3>
            <div class="flex items-center gap-2">
                @foreach(['pending', 'payment_confirmed', 'shipped', 'completed'] as $step)
                    @php
                        $currentIndex = array_search($order->status, ['pending','payment_confirmed','shipped','completed']);
                        $stepIndex = array_search($step, ['pending','payment_confirmed','shipped','completed']);
                        $isActive = $currentIndex >= $stepIndex;
                    @endphp
                    <div class="flex items-center gap-2 flex-1">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full {{ $isActive ? 'bg-emerald-900' : 'bg-stone-200' }}"></div>
                            <p class="text-[9px] font-bold uppercase tracking-wide mt-2 {{ $isActive ? 'text-emerald-900' : 'text-stone-400' }}">
                                {{ str_replace('_', ' ', $step) }}
                            </p>
                        </div>
                        @if(!$loop->last)
                            <div class="h-px flex-1 mb-4 {{ $currentIndex > $stepIndex ? 'bg-emerald-900' : 'bg-stone-200' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <section class="bg-white p-6 md:p-8 rounded-2xl border border-stone-200 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-emerald-900">inventory_2</span>
                <h2 class="text-xl font-bold text-emerald-900">Review Items</h2>
            </div>
            
            <div class="bg-stone-50 p-4 rounded-xl flex gap-6 items-center border border-stone-100">
                <div class="w-24 h-32 rounded-lg overflow-hidden bg-stone-200 flex-shrink-0">
                    @if($order->item->first_photo)
                        <img src="{{ asset('storage/'.$order->item->first_photo) }}"
                             alt="{{ $order->item->item_name }}"
                             class="w-full h-full object-cover">
                    @else
                        <img src="/placeholder.jpg"
                             alt="{{ $order->item->item_name }}"
                             class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="flex-grow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-stone-900">{{ $order->item->item_name }}</h3>
                            <div class="mt-1">
                                @if($order->item->condition === 'new_with_tags')
                                    <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-orange-100 text-orange-800">New With Tags</span>
                                @elseif($order->item->condition === 'like_new')
                                    <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Like New</span>
                                @else
                                    <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-stone-200 text-stone-700">{{ str_replace('_', ' ', $order->item->condition) }}</span>
                                @endif
                            </div>
                        </div>
                        <p class="font-bold text-emerald-900">Rp {{ number_format($order->item->price, 0, ',', '.') }}</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-xs text-stone-500">Seller: {{ $order->seller?->name ?? 'Unknown' }}</p>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="bg-emerald-950 text-emerald-50 p-8 rounded-2xl shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
                <span class="material-symbols-outlined text-[160px] text-emerald-300">eco</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-4">
                    <h3 class="font-bold text-sm uppercase tracking-widest text-emerald-300">Environmental Impact</h3>
                </div>
                <div class="mb-6">
                    <p class="text-5xl font-black text-white mb-2">{{ number_format($order->co2_saved_amount, 1) }} kg</p>
                    <p class="text-emerald-300 text-lg font-bold">CO2 Saved</p>
                </div>
                <p class="text-sm leading-relaxed text-emerald-200/80 max-w-2xl">
                    By choosing this pre-loved item, you've saved the equivalent emissions of driving 50km. You are actively extending the life cycle of a premium garment.
                </p>
            </div>
        </div>

        <div class="bg-white p-6 md:p-8 rounded-2xl border border-stone-200 shadow-sm">
            <h3 class="text-xl font-bold text-emerald-900 mb-6">Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="flex justify-between text-stone-600">
                        <span>Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-stone-600">
                        <span>Carbon Offset</span>
                        <span class="text-emerald-600 font-medium">Free</span>
                    </div>
                    <div class="h-px bg-stone-200 my-4"></div>
                    <div class="flex justify-between items-baseline">
                        <span class="font-bold text-stone-900 text-lg">Total</span>
                        <span class="text-2xl font-black text-emerald-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="flex flex-col gap-3 justify-center">
                    @if(Auth::id() === $order->buyer_id && $order->status === 'pending')
                        <a href="{{ route('orders.payment', $order) }}"
                           class="w-full py-3.5 bg-emerald-900 text-white font-bold rounded-full text-sm shadow-md hover:bg-emerald-800 transition-colors text-center">
                            Confirm Payment →
                        </a>

                        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-3.5 border-2 border-red-100 text-red-500 font-bold rounded-full text-sm hover:bg-red-50 transition-colors">
                                Cancel Order
                            </button>
                        </form>
                    @endif

                    @if(Auth::id() === $order->users_id && $order->status === 'payment_confirmed')
                        <form method="POST" action="{{ route('orders.ship', $order) }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-3.5 bg-emerald-900 text-white font-bold rounded-full text-sm hover:bg-emerald-800 transition-colors">
                                Mark as Shipped
                            </button>
                        </form>
                    @endif

                    @if(Auth::id() === $order->buyer_id && $order->status === 'shipped')
                        <form method="POST" action="{{ route('orders.receive', $order) }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-3.5 bg-emerald-900 text-white font-bold rounded-full text-sm hover:bg-emerald-800 transition-colors">
                                Confirm Received
                            </button>
                        </form>
                    @endif

                    @if($order->status !== 'pending')
                        <a href="{{ route('marketplace.index') }}" class="w-full py-3.5 bg-transparent border-2 border-stone-200 text-stone-600 font-bold rounded-full text-sm hover:bg-stone-50 transition-colors text-center block">
                            Back to Marketplace
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection