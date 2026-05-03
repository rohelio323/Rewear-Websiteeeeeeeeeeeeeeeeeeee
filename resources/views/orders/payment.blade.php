@extends('layouts.app')
@section('title', 'Confirm Payment')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400 mb-1">Order #{{ $order->id }}</p>
        <h1 class="text-2xl font-bold text-stone-900">Confirm Payment</h1>
    </div>

    {{-- Item + Price --}}
    <div class="rounded-2xl bg-white border border-stone-200 p-5 mb-4 flex items-center justify-between">
        <div>
            <p class="font-semibold text-stone-900">{{ $order->item->item_name }}</p>
            <p class="text-xs text-stone-400 mt-0.5">Total to pay</p>
        </div>
        <p class="text-xl font-bold font-mono text-emerald-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
    </div>

    {{-- Form --}}
    <div class="rounded-2xl bg-white border border-stone-200 p-6">
        <p class="text-sm text-stone-400 mb-6">Enter your bank transfer details to confirm payment. The seller will be notified.</p>

        <form method="POST" action="{{ route('orders.confirmPayment', $order) }}" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col gap-4">

                {{-- Bank Name --}}
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1.5 uppercase tracking-widest">Bank Name *</label>
                    <input type="text"
                           name="bank_name"
                           value="{{ old('bank_name') }}"
                           placeholder="e.g., BCA, BNI, Mandiri"
                           required
                           class="w-full border border-stone-200 rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none focus:ring-2 focus:ring-emerald-900">
                    @error('bank_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Transfer Reference --}}
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1.5 uppercase tracking-widest">Transfer Reference / Receipt Number *</label>
                    <input type="text"
                           name="payment_reference"
                           value="{{ old('payment_reference') }}"
                           placeholder="e.g., 20240414123456789"
                           required
                           class="w-full border border-stone-200 rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none focus:ring-2 focus:ring-emerald-900">
                    @error('payment_reference')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Payment Proof Upload --}}
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1.5 uppercase tracking-widest">Payment Proof *</label>
                    <label for="payment_proof"
                        class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-stone-200 rounded-xl cursor-pointer hover:border-emerald-400 hover:bg-emerald-50 transition-colors duration-200">
                        <div id="upload-placeholder" class="flex flex-col items-center">
                            <svg class="w-8 h-8 text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs text-stone-400">Click to upload <span class="text-emerald-700 font-medium">JPG, PNG</span></p>
                            <p class="text-[10px] text-stone-300 mt-1">Max 2MB</p>
                        </div>
                        <img id="preview-img" src="" alt="preview" class="hidden h-32 object-contain rounded-lg">
                        <input id="payment_proof" type="file" name="payment_proof" accept="image/*" class="hidden"
                            onchange="
                                const file = this.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = e => {
                                        document.getElementById('preview-img').src = e.target.result;
                                        document.getElementById('preview-img').classList.remove('hidden');
                                        document.getElementById('upload-placeholder').classList.add('hidden');
                                    };
                                    reader.readAsDataURL(file);
                                }
                            ">
                    </label>
                    @error('payment_proof')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 justify-end mt-2">
                    <a href="{{ route('orders.show', $order) }}"
                       class="flex items-center justify-center border border-stone-200 text-stone-600 hover:bg-stone-50 text-sm font-medium px-6 py-2.5 rounded-full transition-colors duration-200">
                        Back
                    </a>
                    <button type="submit"
                        class="flex items-center justify-center bg-emerald-900 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-full transition-colors duration-200">
                        Confirm Payment
                    </button>
                </div>

            </div>
        </form>
    </div>

</div>
@endsection