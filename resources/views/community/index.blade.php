<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReWear - The Living Archive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8F9FA; } </style>
</head>
<body class="text-gray-900 antialiased">

    <nav class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-8">
            <h1 class="text-xl font-bold text-emerald-900 tracking-tighter">ReWear</h1>
            <div class="hidden md:flex gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('home') ?? '#' }}" class="hover:text-black">Home</a>
                <a href="{{ route('marketplace.index') ?? '#' }}" class="hover:text-black">Marketplace</a>
                <a href="{{ route('community.index') }}" class="text-emerald-800 border-b-2 border-emerald-800 pb-1">Community</a>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @auth
                <span class="text-xs font-semibold text-gray-600">Halo, {{ Auth::user()->name }}</span>
            @else
                <a href="/login" class="text-xs font-bold text-emerald-900">LOGIN</a>
            @endauth
            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-500 text-xs">
                {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="text-4xl font-extrabold mb-2 text-emerald-950">The Living Archive</h1>
        <p class="text-gray-600 mb-8 text-sm">Join our community of conscious curators. Share your repair journey and document the life of your garments.</p>

        <div class="w-full bg-black rounded-3xl overflow-hidden relative h-72 mb-10 shadow-lg hidden md:block">
            <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?q=80&w=1200&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-50" alt="Banner">
            <div class="absolute inset-0 p-10 flex flex-col justify-end">
                <span class="bg-orange-800 text-white text-xs font-bold px-3 py-1 rounded-full w-max mb-3">STORY OF THE WEEK</span>
                <h2 class="text-3xl font-bold text-white mb-2">The Leather Legacy</h2>
                <button class="bg-white text-black font-semibold text-sm px-6 py-2 rounded-full w-max hover:bg-gray-100 transition">Read Story</button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <div class="flex-1">
                
                @forelse($posts as $post)
                <div class="bg-white rounded-3xl p-6 shadow-sm mb-6 border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center font-bold text-gray-600 text-xs border border-gray-200">
                            U{{ $post->users_id }}
                        </div>
                        <div>
                            <p class="font-bold text-sm text-gray-800">User #{{ $post->users_id }}</p>
                            <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold mb-2 text-emerald-950">{{ $post->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
                    
                    @if($post->image_path)
                    <div class="mb-5 rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 relative">
                        <img src="{{ asset('storage/' . $post->image_path) }}" alt="Post image" class="w-full max-h-[400px] object-cover hover:opacity-95 transition">
                    </div>
                    @endif

                    <div class="flex items-center justify-between border-t border-gray-50 pt-4 mt-2">
                        <div class="flex gap-4">
                            <button class="flex items-center gap-1 bg-gray-50 px-3 py-1.5 rounded-full text-xs font-bold hover:bg-emerald-50 transition border border-gray-100">
                                ↑ <span class="text-gray-800">{{ $post->upvote_count ?? 0 }}</span> ↓
                            </button>
                        </div>
                        <span class="text-gray-400 hover:text-emerald-800 transition cursor-pointer text-xs font-bold flex items-center gap-1">
                            🔗 Share
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                    <span class="text-4xl mb-3 block">🌿</span>
                    <h3 class="font-bold text-gray-700">The archive is empty</h3>
                    <p class="text-gray-400 text-sm mt-1">Check back later for sustainable stories!</p>
                </div>
                @endforelse

            </div>

            <div class="w-full lg:w-80 shrink-0">
                <div class="bg-[#F8A676] p-6 rounded-3xl mb-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-widest text-red-900 uppercase mb-2">Tip of the day</p>
                    <h3 class="text-lg font-bold text-red-900 leading-tight">The most sustainable garment is the one already in your closet.</h3>
                </div>

                <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100">
                    <h3 class="font-bold text-sm mb-4 text-gray-800">TOP CURATORS</h3>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 bg-orange-400 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm">1</div>
                        <p class="font-bold text-sm text-gray-700">Julian Vogel</p>
                    </div>
                    <button class="w-full bg-white border border-gray-200 text-[10px] font-bold py-2.5 rounded-full hover:bg-gray-100 hover:text-emerald-900 transition uppercase tracking-wider text-gray-500 shadow-sm">View Leaderboard</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>