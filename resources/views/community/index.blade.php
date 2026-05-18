@extends('layouts.app')

@section('content')
<main class="text-gray-900 antialiased">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

        {{-- Flash Messages & Errors --}}
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-medium shadow-sm">✅ {{ session('success') }}</div>
        @endif
        
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm font-medium shadow-sm">
                <p class="font-bold mb-2">Wait, something went wrong:</p>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Active Challenges Horizontal Scroll --}}
        @if(isset($activeChallenges) && $activeChallenges->isNotEmpty())
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-600 font-label flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">local_fire_department</span>
                    Trending Challenges
                </h3>
                <a href="{{ route('challenges.index') }}" class="text-xs font-bold text-gray-500 hover:text-emerald-700 transition-colors">View All</a>
            </div>
            
            {{-- Horizontal scrolling container --}}
            <div class="flex gap-4 overflow-x-auto pb-4 snap-x hide-scrollbar" style="scrollbar-width: none;">
                @foreach($activeChallenges as $challenge)
                    <a href="{{ route('challenges.show', $challenge->id) }}" class="snap-start flex-shrink-0 bg-emerald-950 rounded-3xl p-6 w-[300px] relative overflow-hidden group shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        {{-- Background Pattern --}}
                        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 20px 20px;"></div>
                        
                        {{-- Content --}}
                        <div class="relative z-10">
                            <span class="inline-block px-2.5 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 rounded-lg text-[10px] font-bold uppercase tracking-wider mb-3 backdrop-blur-sm shadow-sm">
                                #{{ $challenge->hashtag }}
                            </span>
                            <h4 class="text-white font-extrabold text-xl font-headline mb-2 leading-tight">{{ $challenge->title }}</h4>
                            <p class="text-emerald-100/70 text-xs line-clamp-2">{{ $challenge->description }}</p>
                        </div>

                        {{-- Decorative Icon --}}
                        <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-[100px] text-emerald-900/50 group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-500">style</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">
            <div class="flex-1">
                
                <div class="bg-white p-4 rounded-3xl shadow-sm mb-8 border border-gray-100 flex gap-3 cursor-pointer hover:bg-gray-50 transition items-center group" onclick="openModal('createModal')">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full shrink-0 flex items-center justify-center overflow-hidden">
                        <span class="text-emerald-800 font-bold text-sm">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                    <div class="flex-1 bg-gray-100 rounded-full px-5 py-3 text-sm text-gray-400 font-medium cursor-text group-hover:bg-gray-200 transition">
                        What's your sustainable story, {{ explode(' ', Auth::user()->name ?? 'Guest')[0] }}?
                    </div>
                </div>

                @forelse($posts as $post)
                <div class="bg-white rounded-3xl p-6 shadow-sm mb-6 border border-gray-100 relative">
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center font-bold text-gray-600 text-xs border border-gray-200">
                                U{{ $post->users_id }}
                            </div>
                            <div>
                                <p class="font-bold text-sm text-gray-800">User #{{ $post->users_id }}</p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        
                        @auth
                            @if(Auth::id() == $post->users_id || Auth::id() == 1) 
                            <div>
                                <button onclick="toggleDropdown({{ $post->post_id }})" class="kebab-button text-gray-400 hover:text-gray-800 font-bold text-xl px-2 pb-2 transition">⋮</button>
                                
                                <div id="dropdown-{{ $post->post_id }}" class="dropdown-menu hidden absolute right-0 top-8 w-36 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-20">
                                    <button onclick="openEditModal(this)" 
                                            data-id="{{ $post->post_id }}" 
                                            data-title="{{ $post->title }}" 
                                            data-content="{{ $post->content }}" 
                                            data-tags="{{ is_array($post->tags) ? implode(', ', $post->tags) : (is_string($post->tags) ? $post->tags : '') }}"
                                            class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 font-semibold border-b border-gray-50">
                                        ✏️ Edit Post
                                    </button>
                                    <form action="{{ route('community.destroy', $post->post_id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-semibold" onclick="return confirm('Are you sure you want to delete this post?')">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        @endauth
                    </div>

                    <h3 class="text-xl font-bold mb-2 text-emerald-950">{{ $post->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
                    
                    {{-- Render Tags on Post --}}
                    @if(!empty($post->tags))
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @php 
                            $tagsArray = is_array($post->tags) ? $post->tags : (json_decode($post->tags, true) ?? explode(',', $post->tags));
                        @endphp
                        @foreach($tagsArray as $tag)
                            @if(trim($tag) !== '')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                #{{ trim($tag) }}
                            </span>
                            @endif
                        @endforeach
                    </div>
                    @endif

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
                    <p class="text-gray-400 text-sm mt-1">Be the first to share your sustainable story!</p>
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

    {{-- Create Modal --}}
    <div id="createModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl w-full max-w-xl p-6 shadow-2xl relative transform scale-95 transition-transform duration-300 modal-box max-h-[90vh] overflow-y-auto">
            <button type="button" onclick="closeModal('createModal')" class="absolute top-5 right-6 text-gray-400 hover:text-red-500 text-xl font-bold transition z-10">✕</button>
            <h2 class="text-2xl font-bold mb-6 text-emerald-950">Create a Post</h2>
            <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                <input type="text" name="title" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-800 focus:outline-none font-bold placeholder-gray-400" placeholder="Give your story a title..." required>
                <textarea name="content" rows="3" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-800 focus:outline-none resize-none placeholder-gray-400" placeholder="Share your sustainable tips..." required></textarea>
                
                {{-- Hashtag Tag Input --}}
                <div class="bg-emerald-50/60 border border-emerald-100 rounded-xl p-4">
                    <label class="block text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
                        # Challenge Tags
                    </label>

                    @if(isset($activeChallenges) && $activeChallenges->isNotEmpty())
                    <div class="mb-3">
                        <p class="text-[10px] text-emerald-700 font-semibold uppercase tracking-widest mb-2">Active Challenges — click to add:</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($activeChallenges as $ac)
                            <button type="button"
                                onclick="addTag('createTags', '{{ $ac->hashtag }}', 'createTagPreview')"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-900 text-white hover:bg-emerald-700 transition cursor-pointer">
                                🏆 #{{ $ac->hashtag }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <input type="text" name="tags" id="createTags"
                           class="w-full bg-white border border-emerald-200 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-emerald-700 focus:outline-none placeholder-gray-400"
                           placeholder="or type manually: rewear30days, slowfashion"
                           oninput="this.value=this.value.toLowerCase(); previewHashtags(this.value, 'createTagPreview')">
                    
                    <div id="createTagPreview" class="mt-2 space-y-1.5"></div>
                </div>

                {{-- Image Upload Area --}}
                <div class="border-2 border-dashed border-emerald-200 bg-emerald-50/50 rounded-xl p-6 text-center hover:bg-emerald-50 transition cursor-pointer relative group mt-2">
                    <label class="cursor-pointer flex flex-col items-center gap-2 w-full h-full">
                        <span class="text-3xl group-hover:scale-110 transition duration-300">📸</span>
                        <span class="text-xs text-emerald-800 font-semibold">Upload Image (Optional)</span>
                        <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this, 'createFileName', 'createImagePreview')">
                    </label>
                    <p id="createFileName" class="text-xs text-emerald-600 font-bold mt-3 hidden bg-emerald-100 py-1 px-3 rounded-full inline-block"></p>
                    <img id="createImagePreview" class="hidden mt-4 w-full h-40 object-cover rounded-xl border border-emerald-200 shadow-sm" />
                </div>
                <button type="submit" class="bg-emerald-900 text-white px-6 py-3.5 rounded-full text-sm font-bold hover:bg-emerald-800 transition w-full mt-4">Share to Community</button>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl w-full max-w-xl p-6 shadow-2xl relative transform scale-95 transition-transform duration-300 modal-box max-h-[90vh] overflow-y-auto">
            <button type="button" onclick="closeModal('editModal')" class="absolute top-5 right-6 text-gray-400 hover:text-red-500 text-xl font-bold transition z-10">✕</button>
            <h2 class="text-2xl font-bold mb-6 text-emerald-950">Edit Post</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf 
                @method('PUT')
                <input type="text" id="editTitle" name="title" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-800 focus:outline-none font-bold" required>
                <textarea id="editContent" name="content" rows="3" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-800 focus:outline-none resize-none" required></textarea>
                
                {{-- Edit Hashtag Tag Input --}}
                <div class="bg-emerald-50/60 border border-emerald-100 rounded-xl p-4">
                    <label class="block text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2"># Tags</label>
                    <input type="text" name="tags" id="editTags"
                           class="w-full bg-white border border-emerald-200 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-emerald-700 focus:outline-none lowercase placeholder-gray-400"
                           placeholder="rewear30days, slowfashion"
                           oninput="previewHashtags(this.value, 'editTagPreview')">
                    <div id="editTagPreview" class="mt-2 space-y-1.5"></div>
                </div>

                {{-- Image Upload Area --}}
                <div class="border-2 border-dashed border-emerald-200 bg-emerald-50/50 rounded-xl p-6 text-center hover:bg-emerald-50 transition cursor-pointer relative group mt-2">
                    <label class="cursor-pointer flex flex-col items-center gap-2 w-full h-full">
                        <span class="text-3xl group-hover:scale-110 transition duration-300">📸</span>
                        <span class="text-xs text-emerald-800 font-semibold">Upload New Image (Optional)</span>
                        <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this, 'editFileName', 'editImagePreview')">
                    </label>
                    <p id="editFileName" class="text-xs text-emerald-600 font-bold mt-3 hidden bg-emerald-100 py-1 px-3 rounded-full inline-block"></p>
                    <img id="editImagePreview" class="hidden mt-4 w-full h-40 object-cover rounded-xl border border-emerald-200 shadow-sm" />
                </div>
                <button type="submit" class="bg-emerald-900 text-white px-6 py-3.5 rounded-full text-sm font-bold hover:bg-emerald-800 transition w-full mt-4">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
        const LOOKUP_URL = '{{ route("community.hashtag.lookup") }}';
        
        const lookupCache = {};
        let lookupTimer = null;

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

        function openEditModal(btn) {
            const id = btn.getAttribute('data-id');
            document.getElementById('editTitle').value = btn.getAttribute('data-title');
            document.getElementById('editContent').value = btn.getAttribute('data-content');
            
            // Populate existing tags
            const existingTags = btn.getAttribute('data-tags') || '';
            document.getElementById('editTags').value = existingTags;
            if (existingTags) {
                previewHashtags(existingTags, 'editTagPreview');
            } else {
                document.getElementById('editTagPreview').innerHTML = '';
            }

            document.getElementById('editForm').action = `/community/update/${id}`;
            closeAllDropdowns();
            openModal('editModal');
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            const isHidden = dropdown.classList.contains('hidden');
            closeAllDropdowns(); 
            if (isHidden) dropdown.classList.remove('hidden');
        }

        function closeAllDropdowns() {
            document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
        }

        window.onclick = function(event) {
            if (!event.target.matches('.kebab-button') && !event.target.closest('.dropdown-menu')) closeAllDropdowns();
            if (event.target.classList.contains('fixed')) closeModal(event.target.id);
        }

        function previewImage(input, textId, imageId) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = document.getElementById(textId);
                fileName.textContent = '✅ Selected: ' + file.name;
                fileName.classList.remove('hidden');

                if (imageId) {
                    const imgPreview = document.getElementById(imageId);
                    imgPreview.src = URL.createObjectURL(file);
                    imgPreview.classList.remove('hidden');
                }
            }
        }

        function addTag(inputId, tag, previewId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const existing = input.value.split(',').map(t => t.trim().toLowerCase()).filter(Boolean);
            if (!existing.includes(tag)) {
                existing.push(tag);
                input.value = existing.join(', ');
            }
            previewHashtags(input.value, previewId);
            input.focus();
        }

        async function previewHashtags(raw, previewId) {
            const preview = document.getElementById(previewId);
            if (!preview) return;

            const tags = raw.split(',').map(t => t.trim().replace(/[^a-z0-9]/gi, '').toLowerCase()).filter(Boolean);
            if (!tags.length) { preview.innerHTML = ''; return; }

            clearTimeout(lookupTimer);
            lookupTimer = setTimeout(async () => {
                try {
                    const results = await Promise.all(tags.map(async tag => {
                        if (lookupCache[tag] !== undefined) return { tag, challenge: lookupCache[tag] };
                        const r = await fetch(`${LOOKUP_URL}?q=${tag}`);
                        if (!r.ok) throw new Error('Network response was not ok');
                        const d = await r.json();
                        lookupCache[tag] = d.challenge;
                        return { tag, challenge: d.challenge };
                    }));

                    preview.innerHTML = results.map(({ tag, challenge }) => challenge
                        ? `<div class="flex items-center gap-2 bg-emerald-100 border border-emerald-200 rounded-lg px-3 py-1.5">
                            <span class="text-emerald-600 font-mono text-xs font-bold">#${tag}</span>
                            <span class="text-gray-400 text-xs">→</span>
                            <span class="text-emerald-800 font-bold text-xs">🏆 ${challenge.title}</span>
                           </div>`
                        : `<div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                            <span class="text-gray-400 font-mono text-xs">#${tag}</span>
                            <span class="text-gray-400 text-xs italic">— no active challenge</span>
                           </div>`
                    ).join('');
                } catch (error) {
                    preview.innerHTML = tags.map(tag => 
                        `<div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                            <span class="text-gray-600 font-mono text-xs">#${tag}</span>
                        </div>`
                    ).join('');
                }
            }, 300);
        }
    </script>
</main>
@endsection