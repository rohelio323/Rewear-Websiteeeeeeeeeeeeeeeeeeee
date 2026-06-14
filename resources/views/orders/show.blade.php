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
        
        {{-- Status Tracker --}}
        <div class="bg-white p-6 md:p-8 rounded-2xl border border-stone-200 shadow-sm">
            <h3 class="text-xl font-bold text-emerald-900 mb-6 md:mb-8">Order Status</h3>
            
            <div class="flex items-center w-full pb-12 pt-2 px-4 md:px-8">
                @php
                    $statuses = ['pending', 'payment_confirmed', 'shipped', 'completed'];
                    $currentIndex = array_search($order->status, $statuses);
                @endphp

                @foreach($statuses as $index => $step)
                    @php
                        $isActive = $currentIndex >= $index;
                        $isPast = $currentIndex > $index;
                    @endphp

                    <div class="relative flex flex-col items-center z-10 shrink-0">
                        <div class="w-8 h-8 rounded-full border-[3px] flex items-center justify-center {{ $isActive ? 'bg-emerald-600 border-emerald-100 shadow-sm' : 'bg-white border-stone-200' }} transition-colors duration-300">
                            @if($isPast)
                                <span class="material-symbols-outlined text-[14px] text-white font-bold">check</span>
                            @elseif($isActive)
                                <div class="w-2 h-2 rounded-full bg-white"></div>
                            @endif
                        </div>

                        <div class="absolute top-10 text-center w-24 left-1/2 transform -translate-x-1/2">
                            <p class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest {{ $isActive ? 'text-emerald-900' : 'text-stone-400' }} leading-tight whitespace-pre-line">{{ str_replace('_', "\n", $step) }}</p>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <div class="flex-1 h-[3px] mx-2 md:mx-4 rounded-full transition-colors duration-500 {{ $isPast ? 'bg-emerald-500' : 'bg-stone-200' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Review Items --}}
        <section class="bg-white p-6 md:p-8 rounded-2xl border border-stone-200 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-emerald-900">inventory_2</span>
                <h2 class="text-xl font-bold text-emerald-900">Review Items</h2>
            </div>
            <div class="bg-stone-50 p-4 rounded-xl flex gap-6 items-center border border-stone-100">
                <div class="w-24 h-32 rounded-lg overflow-hidden bg-stone-200 flex-shrink-0">
                    @if($order->item->first_photo)
                        <img src="{{ asset('storage/'.$order->item->first_photo) }}" alt="{{ $order->item->item_name }}" class="w-full h-full object-cover">
                    @else
                        <img src="/placeholder.jpg" alt="{{ $order->item->item_name }}" class="w-full h-full object-cover">
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
                    <div class="mt-4">
                        <p class="text-xs text-stone-500">Seller: <span class="font-bold text-stone-700">{{ $order->seller?->name ?? 'Unknown' }}</span></p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Tracking info for buyer when shipped --}}
        @if(in_array($order->status, ['shipped', 'completed']))
            <div class="bg-white p-6 md:p-8 rounded-2xl border border-stone-200 shadow-sm">
                <div class="flex items-center gap-3 mb-5">
                    <span class="material-symbols-outlined text-emerald-900">local_shipping</span>
                    <h2 class="text-xl font-bold text-emerald-900">Shipment Details</h2>
                </div>
                <div class="bg-stone-50 rounded-xl border border-stone-100 p-5 flex flex-col gap-3">
                    <div class="flex justify-between items-center border-b border-stone-200 pb-3 mb-1">
                        <span class="text-xs font-medium text-stone-500 uppercase tracking-widest">Courier</span>
                        <span class="font-bold text-stone-900 text-sm">{{ $order->courier_name ?? 'Standard Delivery' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-stone-500 uppercase tracking-widest">Tracking Number</span>
                        <span class="font-bold text-emerald-900 text-sm">{{ $order->tracking_number ?? 'Not provided yet' }}</span>
                    </div>
                    @if($order->shipping_proof)
                        <div class="pt-4 mt-2 border-t border-stone-200">
                            <p class="text-xs font-medium text-stone-500 uppercase tracking-widest mb-3">Shipping Proof</p>
                            <div class="rounded-xl overflow-hidden border border-stone-200 shadow-inner bg-white">
                                <img src="{{ asset('storage/' . $order->shipping_proof) }}"
                                     alt="Shipping Proof"
                                     class="w-full object-cover max-h-64">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Environmental Impact Card --}}
        <div class="bg-emerald-950 text-emerald-50 p-8 md:p-10 rounded-2xl shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
                <span class="material-symbols-outlined text-[200px] text-emerald-300">eco</span>
            </div>
            
            <div class="relative z-10">
                <h3 class="font-bold text-xs uppercase tracking-widest text-emerald-400 mb-4">Environmental Impact</h3>
                
                <div class="mb-8">
                    <p class="text-5xl md:text-6xl font-black text-white mb-2 tracking-tight">{{ number_format($order->co2_saved_amount, 1) }} kg</p>
                    <p class="text-emerald-400 text-sm font-bold uppercase tracking-widest">CO₂ Saved</p>
                </div>
                
                {{-- Refined Quote Box --}}
                <div class="text-sm leading-relaxed text-emerald-50 max-w-3xl bg-emerald-900/50 p-6 rounded-2xl border border-emerald-800/50 backdrop-blur-sm shadow-inner">
                    @if($order->item->category && $order->item->category->reference_note)
                        <p class="mb-4 italic text-emerald-100">"{{ $order->item->category->reference_note }}"</p>
                        
                        <div class="flex items-center flex-wrap gap-3 pt-4 border-t border-emerald-800/60">
                            <span class="text-emerald-500 font-bold">—</span>
                            <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-widest">{{ $order->item->category->category_name }} Data</p>
                            
                            @if(!empty($order->item->category->reference_url))
                                <span class="w-1 h-1 rounded-full bg-emerald-600"></span>
                                <a href="{{ $order->item->category->reference_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-300 hover:text-white transition-colors underline decoration-emerald-500/30 underline-offset-4">
                                    View Source <span class="material-symbols-outlined text-[12px]">open_in_new</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <p class="mb-4 text-emerald-100">By choosing this pre-loved item, you've saved the equivalent emissions of driving roughly <span class="font-bold text-white">{{ number_format($order->co2_saved_amount * 4) }}km</span> in a standard passenger car. You are actively extending the life cycle of a premium garment!</p>
                        
                        <div class="flex items-center flex-wrap gap-3 pt-4 border-t border-emerald-800/60">
                            <span class="text-emerald-500 font-bold">—</span>
                            <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-widest">EPA Equivalency Estimate</p>
                            <span class="w-1 h-1 rounded-full bg-emerald-600"></span>
                            <a href="https://www.epa.gov/energy/greenhouse-gas-equivalencies-calculator" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-300 hover:text-white transition-colors underline decoration-emerald-500/30 underline-offset-4">
                                Calculator <span class="material-symbols-outlined text-[12px]">open_in_new</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Summary & Actions --}}
        <div class="bg-white p-6 md:p-8 rounded-2xl border border-stone-200 shadow-sm">
            <h3 class="text-xl font-bold text-emerald-900 mb-6">Summary</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between text-stone-600 text-sm md:text-base">
                    <span>Subtotal</span>
                    <span class="font-medium">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                
                {{-- Hover Tooltip for Carbon Offset --}}
                <div class="flex justify-between text-stone-600 items-center group relative text-sm md:text-base">
                    <div class="flex items-center gap-1.5 cursor-help">
                        <span>Carbon Offset</span>
                        <span class="material-symbols-outlined text-[14px] text-stone-400 mt-0.5">help</span>
                        <div class="absolute bottom-full left-0 mb-2 hidden w-48 bg-stone-800 text-white text-[10px] p-2.5 rounded-lg shadow-lg group-hover:block z-10 leading-relaxed">
                            Buying pre-loved inherently offsets manufacturing emissions. No extra fees needed!
                        </div>
                    </div>
                    <span class="text-emerald-600 font-bold tracking-wide">Free</span>
                </div>

                <div class="h-px bg-stone-200 my-4"></div>
                <div class="flex justify-between items-baseline">
                    <span class="font-bold text-stone-900 text-lg">Total</span>
                    <span class="text-2xl font-black text-emerald-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            @if(Auth::id() === $order->users_id && $order->payment_proof)
                <div class="pt-6 border-t border-stone-100 mt-6">
                    <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mb-3 mt-2">Your Payment Proof</p>
                    <div class="rounded-xl overflow-hidden border border-stone-200 bg-stone-50 p-2 max-w-sm">
                        <img src="{{ asset('storage/' . $order->payment_proof) }}"
                             alt="Payment Proof"
                             class="w-full object-cover max-h-56 rounded-lg">
                    </div>
                    @if($order->payment_reference)
                        <p class="text-xs text-stone-500 mt-3 font-mono font-bold">Ref: {{ $order->payment_reference }}</p>
                    @endif
                </div>
            @endif

            {{-- Action Buttons (Aligned to the right at the bottom) --}}
            <div class="mt-8 pt-6 border-t border-stone-100 flex flex-col sm:flex-row justify-end gap-3">
                @if(Auth::id() === $order->buyer_id && $order->status === 'pending')
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-white border border-red-200 text-red-600 font-bold rounded-xl text-sm hover:bg-red-50 transition-colors active:scale-95">
                            Cancel Order
                        </button>
                    </form>
                    <a href="{{ route('orders.payment', $order) }}"
                       class="w-full sm:w-auto px-8 py-3.5 bg-emerald-900 text-white font-bold rounded-xl text-sm shadow-md hover:bg-emerald-800 transition-colors text-center active:scale-95">
                        Confirm Payment →
                    </a>
                @endif

                @if(Auth::id() === $order->users_id && $order->status === 'payment_confirmed')
                    <button type="button" onclick="document.getElementById('ship-form').classList.toggle('hidden')"
                        class="w-full sm:w-auto px-8 py-3.5 bg-emerald-900 text-white font-bold rounded-xl text-sm hover:bg-emerald-800 shadow-md transition-colors active:scale-95">
                        Mark as Shipped →
                    </button>
                @endif

                @if(Auth::id() === $order->buyer_id && $order->status === 'shipped')
                    <form method="POST" action="{{ route('orders.receive', $order) }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-900 text-white font-bold rounded-xl text-sm shadow-md hover:bg-emerald-800 transition-colors active:scale-95">
                            Confirm Received
                        </button>
                    </form>
                @endif

                    {{-- PBI-38: Review Button --}}
                    @if(Auth::id() === $order->buyer_id && $order->status === 'completed')
                        @if($order->review)
                            <div class="w-full py-3.5 bg-stone-50 border-2 border-stone-200 text-stone-400 font-bold rounded-full text-sm text-center">
                                ★ Review Submitted
                            </div>
                        @else
                            <a href="{{ route('reviews.create', $order) }}"
                               class="w-full py-3.5 bg-amber-500 hover:bg-amber-400 text-white font-bold rounded-full text-sm transition-colors text-center block">
                                ★ Leave a Review
                            </a>
                        @endif
                    @endif

                    @if($order->status !== 'pending')
                        <a href="{{ route('marketplace.index') }}" class="w-full py-3.5 bg-transparent border-2 border-stone-200 text-stone-600 font-bold rounded-full text-sm hover:bg-stone-50 transition-colors text-center block">
                            Back to Marketplace
                        </a>
                    @endif
                @endif

                @if($order->status !== 'pending')
                    <a href="{{ route('marketplace.index') }}" class="w-full sm:w-auto px-8 py-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 text-stone-600 font-bold rounded-xl text-sm transition-colors text-center block active:scale-95">
                        Back to Marketplace
                    </a>
                @endif
            </div>

            @if(Auth::id() === $order->users_id && $order->status === 'payment_confirmed')
                <div id="ship-form" class="hidden mt-6">
                    <div class="bg-stone-50 rounded-2xl border border-stone-200 p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="material-symbols-outlined text-emerald-900">box</span>
                            <h3 class="text-lg font-bold text-emerald-900">Enter Shipment Details</h3>
                        </div>
                        <p class="text-sm text-stone-500 mb-6">Provide the tracking details and upload proof of shipment. The buyer will be notified immediately.</p>
                        
                        <form method="POST" action="{{ route('orders.ship', $order) }}" enctype="multipart/form-data" class="flex flex-col gap-5">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[11px] font-bold text-stone-500 mb-2 uppercase tracking-widest">Courier Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="courier_name" value="{{ old('courier_name') }}"
                                           placeholder="e.g., JNE, SiCepat, J&T" required
                                           class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-900 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent bg-white shadow-sm">
                                    @error('courier_name')
                                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-stone-500 mb-2 uppercase tracking-widest">Tracking Number <span class="text-red-500">*</span></label>
                                    <input type="text" name="tracking_number" value="{{ old('tracking_number') }}"
                                           placeholder="e.g., JNE1234567890" required
                                           class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-900 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-transparent bg-white shadow-sm font-mono">
                                    @error('tracking_number')
                                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-stone-500 mb-2 uppercase tracking-widest">Shipping Proof (Receipt) <span class="text-red-500">*</span></label>
                                <label for="shipping_proof"
                                    class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-stone-300 rounded-xl cursor-pointer hover:border-emerald-500 hover:bg-emerald-50/50 transition-all duration-200 bg-white">
                                    <div id="ship-upload-placeholder" class="flex flex-col items-center">
                                        <span class="material-symbols-outlined text-4xl text-stone-300 mb-2">upload_file</span>
                                        <p class="text-sm font-bold text-stone-600">Click to upload image</p>
                                        <p class="text-xs font-medium text-stone-400 mt-1">JPG, PNG up to 2MB</p>
                                    </div>
                                    <img id="ship-preview-img" src="" alt="preview" class="hidden h-36 w-full object-contain p-2 rounded-xl">
                                    <input id="shipping_proof" type="file" name="shipping_proof" accept="image/*" class="hidden" required
                                        onchange="
                                            const file = this.files[0];
                                            if (file) {
                                                const reader = new FileReader();
                                                reader.onload = e => {
                                                    document.getElementById('ship-preview-img').src = e.target.result;
                                                    document.getElementById('ship-preview-img').classList.remove('hidden');
                                                    document.getElementById('ship-upload-placeholder').classList.add('hidden');
                                                };
                                                reader.readAsDataURL(file);
                                            }
                                        ">
                                </label>
                                @error('shipping_proof')
                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-3 justify-end mt-4 pt-4 border-t border-stone-200">
                                <button type="button" onclick="document.getElementById('ship-form').classList.add('hidden')"
                                    class="bg-white border border-stone-200 text-stone-600 hover:bg-stone-50 text-sm font-bold px-6 py-3 rounded-xl transition-colors active:scale-95 shadow-sm">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-bold px-6 py-3 rounded-xl transition-colors active:scale-95 shadow-md">
                                    Confirm Shipment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</main>
@endsection