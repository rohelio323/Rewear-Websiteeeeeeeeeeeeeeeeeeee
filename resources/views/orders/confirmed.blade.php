@extends('layouts.app')
@section('title', 'Order Confirmed')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16">

    {{-- Check icon --}}
    <div class="flex justify-center mb-6">
        <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center">
            <svg class="w-7 h-7 text-emerald-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
    </div>

    {{-- Title --}}
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-stone-900 mb-2">Order Confirmed</h1>
        <p class="text-stone-400 text-sm">Thank you for being part of the cycle. Your piece of the archive is on its way.</p>
    </div>

    {{-- Order Number + CO2 Grid --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="rounded-2xl bg-white border border-stone-200 p-5">
            <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400 mb-3">Order Number</p>
            <p class="text-lg font-bold font-mono text-stone-900 mb-4">#RW-{{ strtoupper(str_pad($order->id, 6, '0', STR_PAD_LEFT)) }}</p>
            <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400 mb-1">Date</p>
            <p class="text-sm font-medium text-stone-700 mb-4">{{ $order->updated_at->format('d M Y') }}</p>
            @if($order->payment_reference)
            <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400 mb-1">Payment Ref</p>
            <p class="text-xs font-mono text-stone-600">{{ $order->payment_reference }}</p>
            @endif
        </div>

        <div class="rounded-2xl bg-emerald-950 p-5 flex flex-col justify-center">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <p class="text-[11px] font-medium uppercase tracking-widest text-emerald-400">Your Impact</p>
            </div>
            <p class="text-2xl font-bold text-white leading-snug mb-2">
                You saved {{ number_format($order->co2_saved_amount, 1) }}kg of<br>CO2 emissions.
            </p>
            <p class="text-xs text-emerald-400 leading-relaxed">
                By choosing pre-loved over new, you've reduced the planetary cost of this garment to nearly zero.
            </p>
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="rounded-2xl bg-white border border-stone-200 p-5 mb-6">
        <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400 mb-4">Order Summary</p>

        {{-- Item Row with product image --}}
        <div class="flex items-center gap-4 pb-5 mb-5 border-b border-stone-100">
            @if($order->item->first_photo)
                <img src="{{ asset('storage/'.$order->item->first_photo) }}"
                     class="w-20 h-20 object-cover rounded-xl flex-shrink-0">
            @else
                <div class="w-20 h-20 rounded-xl bg-stone-100 flex items-center justify-center flex-shrink-0">
                <img src="/placeholder.jpg"
                    alt="{{ $order->item->item_id }}"
                    class="w-full h-full object-cover">
                </div>
            @endif
            <div class="flex-1">
                <p class="text-sm font-semibold text-stone-900 mb-1">{{ $order->item->item_name }}</p>
                <p class="text-xs text-stone-400 mb-2">{{ $order->item->category?->category_name ?? '—' }} · Size {{ $order->item->size ?? '—' }}</p>
                @if($order->item->condition === 'new_with_tags')
                    <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-orange-100 text-orange-800">New With Tags</span>
                @elseif($order->item->condition === 'like_new')
                    <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Like New</span>
                @else
                    <span class="text-[10px] font-medium uppercase tracking-widest px-2 py-0.5 rounded-full bg-stone-100 text-stone-500">{{ str_replace('_', ' ', $order->item->condition) }}</span>
                @endif
            </div>
            <p class="text-sm font-bold text-stone-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>

        {{-- Totals --}}
        <div class="flex flex-col gap-2">
            <div class="flex justify-between text-sm text-stone-500">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm text-stone-500">
                <span>Shipping</span>
                <span class="text-emerald-700 font-medium">Gratis (Carbon Neutral)</span>
            </div>
            <div class="flex justify-between text-base font-bold text-stone-900 pt-3 border-t border-stone-100 mt-1">
                <span>Total</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-3 justify-center">
        <a href="{{ route('orders.show', $order) }}"
           class="flex items-center justify-center gap-2 bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-full transition-colors duration-200">
            View Order in Dashboard ✦
        </a>
        <a href="{{ route('marketplace.index') }}"
           class="flex items-center justify-center border border-stone-200 text-stone-600 hover:bg-stone-50 text-sm font-medium px-6 py-2.5 rounded-full transition-colors duration-200">
            Continue Shopping →
        </a>
    </div>

</div>
@endsection