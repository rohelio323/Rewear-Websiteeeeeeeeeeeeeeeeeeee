@extends('layouts.app')

@section('title', 'My Reviews')

@section('content')
<main class="pt-10 pb-24 px-4 max-w-[800px] mx-auto min-h-screen">

    {{-- Header --}}
    <div class="mb-8">
        <p class="text-[11px] font-medium uppercase tracking-widest text-stone-400 mb-1">Seller Dashboard</p>
        <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 mb-2">My Reviews</h1>
        <p class="text-stone-500 font-medium">Feedback from your buyers.</p>
    </div>

    {{-- Summary Card --}}
    <div class="bg-emerald-950 text-emerald-50 p-8 rounded-2xl shadow-lg relative overflow-hidden mb-8">
        <div class="absolute top-0 right-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
            <span class="material-symbols-outlined text-[160px] text-amber-300" style="font-variation-settings: 'FILL' 1;">star</span>
        </div>
        <div class="relative z-10 flex items-center gap-8">
            <div>
                <p class="text-5xl font-black text-white">{{ $avgRating }}</p>
                <div class="flex gap-0.5 mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="material-symbols-outlined text-amber-400 text-lg" style="font-variation-settings: 'FILL' {{ $i <= round($avgRating) ? 1 : 0 }};">star</span>
                    @endfor
                </div>
            </div>
            <div class="h-12 w-px bg-emerald-800"></div>
            <div>
                <p class="text-3xl font-bold text-white">{{ $totalReviews }}</p>
                <p class="text-emerald-300 text-sm font-bold uppercase tracking-widest mt-1">Total Reviews</p>
            </div>
        </div>
    </div>

    {{-- Review List --}}
    @if($reviews->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-stone-200">
            <span class="material-symbols-outlined text-stone-300 mb-4" style="font-size:3rem;">reviews</span>
            <h5 class="font-bold text-lg text-stone-900 mb-2">No reviews yet</h5>
            <p class="text-stone-400 text-sm">Reviews from completed orders will appear here.</p>
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach($reviews as $review)
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-secondary-fixed ring-2 ring-white flex-shrink-0">
                                <img class="w-full h-full object-cover"
                                     src="{{ $review->buyer->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->buyer->name) }}"
                                     alt="{{ $review->buyer->name }}">
                            </div>
                            <div>
                                <p class="font-bold text-sm text-stone-900">{{ $review->buyer->name }}</p>
                                <p class="text-xs text-stone-400">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-amber-500 text-base" style="font-variation-settings: 'FILL' {{ $i <= $review->rating ? 1 : 0 }};">star</span>
                            @endfor
                        </div>
                    </div>

                    @if($review->comment)
                        <p class="text-sm text-stone-600 leading-relaxed mb-3">{{ $review->comment }}</p>
                    @endif

                    <div class="flex items-center gap-2 pt-3 border-t border-stone-100">
                        <div class="w-8 h-10 rounded overflow-hidden bg-stone-100 flex-shrink-0">
                            @if($review->item->first_photo)
                                <img src="{{ asset('storage/'.$review->item->first_photo) }}" class="w-full h-full object-cover" alt="{{ $review->item->item_name }}">
                            @else
                                <img src="/placeholder.jpg" class="w-full h-full object-cover" alt="{{ $review->item->item_name }}">
                            @endif
                        </div>
                        <p class="text-xs text-stone-400">{{ $review->item->item_name }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center mt-8">
            {{ $reviews->links() }}
        </div>
    @endif

</main>
@endsection