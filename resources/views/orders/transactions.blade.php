@extends('layouts.app')
@section('title', 'My Orders')

@section('content')
<main class="pt-10 pb-24 px-4 max-w-[900px] mx-auto min-h-screen">

    @php
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'payment_confirmed' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-emerald-100 text-emerald-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];
    @endphp

    <div class="mb-8">
        <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400 mb-1">Account</p>
        <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 mb-2">My Orders</h1>
        <p class="text-stone-500 font-medium">Track your purchases and sales.</p>
    </div>

    {{-- BUYING --}}
    <div class="mb-10">
        <h2 class="text-lg font-bold text-emerald-900 mb-4">Purchases</h2>

        @forelse($buying as $order)
        <a href="{{ route('orders.show', $order) }}"
           class="flex items-center gap-4 bg-white border border-stone-200 rounded-2xl p-4 mb-3 hover:border-emerald-300 hover:shadow-sm transition-all">
            <div class="w-16 h-16 rounded-xl overflow-hidden bg-stone-100 flex-shrink-0">
                <img src="{{ $order->item->photo_url }}" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-stone-900 truncate">{{ $order->item->item_name }}</p>
                <p class="text-xs text-stone-400 mt-0.5">Seller: {{ $order->seller?->name ?? '—' }}</p>
                <p class="text-xs text-stone-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>
            <div class="flex-shrink-0 text-right">
                <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-full {{ $colors[$order->status] ?? 'bg-stone-100 text-stone-600' }}">
                    {{ str_replace('_', ' ', $order->status) }}
                </span>
                @if($order->status === 'shipped')
                    <p class="text-[10px] text-emerald-700 font-bold mt-1">Action needed →</p>
                @endif
            </div>
        </a>
        @empty
        <p class="text-stone-400 text-sm">No purchases yet.</p>
        @endforelse
    </div>

    {{-- SELLING --}}
    <div>
        <h2 class="text-lg font-bold text-emerald-900 mb-4">Sales</h2>

        @forelse($selling as $order)
        <a href="{{ route('orders.show', $order) }}"
           class="flex items-center gap-4 bg-white border border-stone-200 rounded-2xl p-4 mb-3 hover:border-emerald-300 hover:shadow-sm transition-all">
            <div class="w-16 h-16 rounded-xl overflow-hidden bg-stone-100 flex-shrink-0">
                <img src="{{ $order->item->photo_url }}" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-stone-900 truncate">{{ $order->item->item_name }}</p>
                <p class="text-xs text-stone-400 mt-0.5">Buyer: {{ $order->buyer?->name ?? '—' }}</p>
                <p class="text-xs text-stone-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>
            <div class="flex-shrink-0 text-right">
                <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-full {{ $colors[$order->status] ?? 'bg-stone-100 text-stone-600' }}">
                    {{ str_replace('_', ' ', $order->status) }}
                </span>
                @if($order->status === 'payment_confirmed')
                    <p class="text-[10px] text-blue-700 font-bold mt-1">Ready to ship →</p>
                @endif
            </div>
        </a>
        @empty
        <p class="text-stone-400 text-sm">No sales yet.</p>
        @endforelse
    </div>

</main>
@endsection