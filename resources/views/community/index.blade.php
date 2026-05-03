@extends('layouts.app')

@section('content')
<main class="text-gray-900 antialiased">

    <div class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="text-4xl font-extrabold mb-2 text-emerald-950">The Living Archive</h1>
        <p class="text-gray-600 mb-8 text-sm">Join our community of conscious curators. Share your repair journey and document the life of your garments.</p>

        <!-- Banner Story of the Week -->
        <div class="w-full bg-black rounded-3xl overflow-hidden relative h-72 mb-10 shadow-lg hidden md:block">
            <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?q=80&w=1200&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-50" alt="Banner">
            <div class="absolute inset-0 p-10 flex flex-col justify-end">
                <span class="bg-orange-800 text-white text-xs font-bold px-3 py-1 rounded-full w-max mb-3">STORY OF THE WEEK</span>
                <h2 class="text-3xl font-bold text-white mb-2">The Leather Legacy</h2>
                <button class="bg-white text-black font-semibold text-sm px-6 py-2 rounded-full w-max hover:bg-gray-100 transition">Read Story</button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Kolom Kiri: Feed -->
            <div class="flex-1">
                
                <!-- [TAMBAHAN TAHAP 2] KOTAK PANCINGAN CREATE POST -->
                <div class="bg-white p-4 rounded-3xl shadow-sm mb-8 border border-gray-100 flex gap-3 cursor-pointer hover:bg-gray-50 transition items-center group" onclick="openModal('createModal')">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full shrink-0 flex items-center justify-center overflow-hidden">
                        <span class="text-emerald-800 font-bold text-sm">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                    <div class="flex-1 bg-gray-100 rounded-full px-5 py-3 text-sm text-gray-400 font-medium cursor-text group-hover:bg-gray-200 transition">
                        What's your sustainable story, {{ explode(' ', Auth::user()->name ?? 'Guest')[0] }}?
                    </div>
                </div>

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

            <!-- Kolom Kanan: Sidebar -->
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

    <!-- [TAMBAHAN TAHAP 2] MODAL CREATE POST -->
    <div id="createModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl w-full max-w-xl p-6 shadow-2xl relative transform scale-95 transition-transform duration-300 modal-box">
            <button onclick="closeModal('createModal')" class="absolute top-5 right-6 text-gray-400 hover:text-red-500 text-xl font-bold transition">✕</button>
            <h2 class="text-2xl font-bold mb-6 text-emerald-950">Create a Post</h2>
            <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                <input type="text" name="title" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-800 focus:outline-none font-bold placeholder-gray-400" placeholder="Give your story a title..." required>
                <textarea name="content" rows="4" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-800 focus:outline-none resize-none placeholder-gray-400" placeholder="Share your sustainable tips..." required></textarea>
                <div class="border-2 border-dashed border-emerald-200 bg-emerald-50/50 rounded-xl p-6 text-center hover:bg-emerald-50 transition cursor-pointer relative group mt-2">
                    <label class="cursor-pointer flex flex-col items-center gap-2 w-full h-full">
                        <span class="text-3xl group-hover:scale-110 transition duration-300">📸</span>
                        <span class="text-xs text-emerald-800 font-semibold">Upload Image</span>
                        <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this, 'createFileName')">
                    </label>
                    <p id="createFileName" class="text-xs text-emerald-600 font-bold mt-3 hidden bg-emerald-100 py-1 px-3 rounded-full inline-block"></p>
                </div>
                <button type="submit" class="bg-emerald-900 text-white px-6 py-3.5 rounded-full text-sm font-bold hover:bg-emerald-800 transition w-full mt-4">Share to Community</button>
            </form>
        </div>
    </div>

    <!-- [TAMBAHAN TAHAP 2] JAVASCRIPT MODAL -->
    <script>
        function openModal(modalId) { 
            const modal = document.getElementById(modalId);
            const box = modal.querySelector('.modal-box');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }, 10);
        }

        function closeModal(modalId) { 
            const modal = document.getElementById(modalId);
            const box = modal.querySelector('.modal-box');
            modal.classList.add('opacity-0');
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('fixed')) {
                closeModal(event.target.id);
            }
        }

        function previewImage(input, textId) {
            const fileName = document.getElementById(textId);
            if (input.files && input.files[0]) {
                fileName.textContent = '✅ Selected: ' + input.files[0].name;
                fileName.classList.remove('hidden');
            }
        }
    </script>

</main>
@endsection