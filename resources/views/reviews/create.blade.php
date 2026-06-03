@extends('layouts.app')

@section('title', 'Leave a Review')

@section('content')
<main class="pt-10 pb-24 px-4 max-w-[600px] mx-auto min-h-screen">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('orders.show', $order) }}"
           class="group inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors mb-6">
            <span class="material-symbols-outlined transition-transform group-hover:-translate-x-1">arrow_back</span>
            Back to Order
        </a>
        <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400 mb-1">Order #{{ $order->id }}</p>
        <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 mb-2">Leave a Review</h1>
        <p class="text-stone-500 font-medium">Share your experience with this seller.</p>
    </div>

    {{-- Item Preview --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-5 mb-6 flex gap-4 items-center">
        <div class="w-16 h-20 rounded-xl overflow-hidden bg-stone-100 flex-shrink-0">
            @if($order->item->first_photo)
                <img src="{{ asset('storage/'.$order->item->first_photo) }}"
                     alt="{{ $order->item->item_name }}"
                     class="w-full h-full object-cover">
            @else
                <img src="/placeholder.jpg" alt="{{ $order->item->item_name }}" class="w-full h-full object-cover">
            @endif
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-1">Item Purchased</p>
            <h3 class="font-bold text-stone-900">{{ $order->item->item_name }}</h3>
            <p class="text-xs text-stone-400 mt-1">Seller: {{ $order->seller?->name ?? 'Unknown' }}</p>
        </div>
    </div>

    {{-- Review Form --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 md:p-8">
        <form action="{{ route('reviews.store', $order) }}" method="POST">
            @csrf

            {{-- Star Rating --}}
            <div class="mb-8">
                <label class="block text-xs font-bold uppercase tracking-widest text-stone-500 mb-4">
                    Your Rating <span class="text-red-400">*</span>
                </label>
                <div class="flex gap-2" id="star-container">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button"
                            class="star-btn text-4xl transition-all duration-150 hover:scale-110"
                            data-value="{{ $i }}">
                            ☆
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}">
                @error('rating')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Comment --}}
            <div class="mb-8">
                <label class="block text-xs font-bold uppercase tracking-widest text-stone-500 mb-3">
                    Your Review <span class="text-stone-300">(optional)</span>
                </label>
                <textarea
                    name="comment"
                    rows="4"
                    maxlength="1000"
                    placeholder="Share your experience — quality, packaging, seller communication..."
                    class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-900 focus:outline-none focus:ring-2 focus:ring-emerald-900 resize-none"
                >{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <a href="{{ route('orders.show', $order) }}"
                   class="flex-1 py-3.5 border-2 border-stone-200 text-stone-600 font-bold rounded-full text-sm hover:bg-stone-50 transition-colors text-center">
                    Cancel
                </a>
                <button type="submit"
                    id="submit-btn"
                    disabled
                    class="flex-1 py-3.5 bg-emerald-900 text-white font-bold rounded-full text-sm hover:bg-emerald-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    Submit Review
                </button>
            </div>
        </form>
    </div>

</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    const submitBtn = document.getElementById('submit-btn');
    let selected = {{ old('rating', 0) }};

    function renderStars(hovered) {
        stars.forEach((star, index) => {
            if (index + 1 <= hovered) {
                star.textContent = '★';
                star.style.color = '#f59e0b';
            } else {
                star.textContent = '☆';
                star.style.color = '#d6d3d1';
            }
        });
    }

    stars.forEach((star, index) => {
        star.addEventListener('mouseenter', () => renderStars(index + 1));
        star.addEventListener('mouseleave', () => renderStars(selected));
        star.addEventListener('click', () => {
            selected = index + 1;
            ratingInput.value = selected;
            renderStars(selected);
            submitBtn.disabled = false;
        });
    });

    if (selected > 0) {
        renderStars(selected);
        submitBtn.disabled = false;
    } else {
        renderStars(0);
    }
});
</script>
@endpush