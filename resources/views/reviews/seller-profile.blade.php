@extends('layouts.app')

@section('content')
<main class="pt-10 pb-24 px-4 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <div class="w-14 h-14 rounded-full overflow-hidden bg-stone-200 ring-2 ring-white shadow">
            <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                 alt="{{ $user->name }}" class="w-full h-full object-cover">
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 flex items-center gap-1">
                {{ $user->name }}
                <span class="material-symbols-outlined text-emerald-600 text-lg" style="font-variation-settings: 'FILL' 1;">verified</span>
            </h1>
            <div class="flex items-center gap-2 mt-1">
                @if($totalReviews > 0)
                    <span class="material-symbols-outlined text-amber-500 text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="font-bold text-stone-800">{{ $avgRating }}</span>
                    <span class="text-stone-400 text-sm">({{ $totalReviews }} reviews)</span>
                @else
                    <span class="text-stone-400 text-sm italic">No reviews yet</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Reviews List --}}
    @if($reviews->count() > 0)
        <div class="flex flex-col gap-4">
            @foreach($reviews as $review)
                <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                {{ substr($review->buyer->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-stone-800">{{ $review->buyer->name ?? 'Anonymous' }}</p>
                                <p class="text-[10px] text-stone-400">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-sm {{ $i <= $review->rating ? 'text-amber-500' : 'text-stone-200' }}"
                                      style="font-variation-settings: 'FILL' 1;">star</span>
                            @endfor
                        </div>
                    </div>
                    <p class="text-sm text-stone-600 leading-relaxed">{{ $review->comment }}</p>
                    @if($review->item)
                        <p class="text-[10px] text-stone-400 mt-3 border-t border-stone-100 pt-2">
                            Item: {{ $review->item->item_name }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $reviews->links() }}</div>
    @else
        <div class="text-center py-16 text-stone-400">
            <span class="material-symbols-outlined text-4xl mb-2 block">rate_review</span>
            <p class="font-medium">No reviews yet</p>
        </div>
    @endif

</main>
@endsection