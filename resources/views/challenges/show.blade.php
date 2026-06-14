@extends('layouts.app')
@section('title', $challenge->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 font-body">
    
    {{-- Back Button --}}
    <a href="{{ route('challenges.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-stone-500 hover:text-stone-900 transition-colors mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back to Challenges
    </a>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-8 flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Challenge Header --}}
    <div class="bg-stone-900 rounded-3xl p-8 md:p-12 text-white mb-12 relative overflow-hidden shadow-xl">
        <div class="relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-bold uppercase tracking-wider mb-4">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Active Event
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold font-headline mb-4">{{ $challenge->title }}</h1>
            <p class="text-lg text-stone-300 max-w-2xl mb-6">{{ $challenge->description }}</p>
            
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-2">
                <div class="flex items-center gap-2 text-sm font-mono text-emerald-400 font-bold">
                    <span class="material-symbols-outlined text-[18px]">timer</span>
                    Submissions close {{ \Carbon\Carbon::parse($challenge->end_date)->format('F j, Y') }}
                </div>
                
                <div class="hidden sm:block text-stone-600">|</div>

                <div>
                    <span class="inline-block bg-white text-stone-900 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm">
                        Tag: #{{ $challenge->hashtag }}
                    </span>
                </div>
            </div>

            {{-- NEW: Reward Badge (Dark Theme) --}}
            @if($challenge->reward_points > 0)
            <div class="mt-6 inline-flex items-center gap-4 p-3 pr-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 backdrop-blur-md shadow-lg">
                <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
                    <span class="material-symbols-outlined text-[20px]">stars</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-500/80 mb-0.5">Challenge Reward</p>
                    <p class="text-base font-extrabold text-amber-400 font-headline leading-none">
                        +{{ $challenge->reward_points }} CO₂ Saved
                        @if($challenge->reward_description)
                            <span class="text-xs font-medium text-amber-200/80 ml-1 font-body">({{ $challenge->reward_description }})</span>
                        @endif
                    </p>
                </div>
            </div>
            @endif

        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        {{-- Left Column: Submission Form --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm sticky top-6">
                <h2 class="text-xl font-extrabold text-stone-900 font-headline mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">add_photo_alternate</span>
                    Submit Your Fit
                </h2>
                
                <form action="{{ route('challenges.submit', $challenge->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-1 font-label">Outfit Title</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-stone-50 focus:bg-white transition-colors" placeholder="e.g., My Vintage Denim Tote">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-1 font-label">Story / Caption</label>
                        <textarea name="content" required rows="3" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-stone-50 focus:bg-white transition-colors resize-none" placeholder="How did you upcycle this item?"></textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-1 font-label">Extra Tags (Optional)</label>
                        <input type="text" name="tags" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-stone-50 focus:bg-white transition-colors" placeholder="e.g., vintage, slowfashion">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-1 font-label">Upload Photo</label>
                        <input type="file" name="image" accept="image/*" required class="w-full text-sm text-stone-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-colors cursor-pointer">
                    </div>

                    <button type="submit" class="w-full py-3.5 px-4 bg-emerald-950 hover:bg-emerald-800 text-white rounded-xl text-sm font-bold shadow-md active:scale-95 transition-all mt-4">
                        Post to Challenge
                    </button>
                </form>
            </div>
        </div>

        {{-- Right Column: Community Gallery --}}
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-extrabold text-stone-900 font-headline mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-stone-400">grid_view</span>
                Community Submissions
            </h2>

            @if($posts->isEmpty())
                <div class="bg-stone-50 rounded-3xl border border-stone-200 border-dashed p-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center mb-4 shadow-sm">
                        <span class="material-symbols-outlined text-3xl text-stone-300">photo_library</span>
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 font-headline mb-1">No entries yet</h3>
                    <p class="text-stone-500 text-sm">Be the first to step up to the challenge!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($posts as $post)
                        <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm group">
                            <div class="aspect-square bg-stone-100 relative overflow-hidden">
                                <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div class="p-5">
                                <h3 class="font-bold text-stone-900 text-lg font-headline mb-1">{{ $post->title }}</h3>
                                <p class="text-xs text-stone-500 mb-3 line-clamp-2">{{ $post->content }}</p>
                                <div class="flex items-center justify-between pt-3 border-t border-stone-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-[10px]">
                                            {{ substr($post->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-stone-600">{{ $post->user->name ?? 'Anonymous' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-stone-400">
                                        <span class="material-symbols-outlined text-[16px]">favorite</span>
                                        <span class="text-xs font-bold">{{ $post->upvote_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection