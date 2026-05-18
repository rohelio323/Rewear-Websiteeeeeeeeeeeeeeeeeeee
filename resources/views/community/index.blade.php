@extends('layouts.app')

@section('content')
<main class="pt-10 pb-20 px-6 max-w-screen-2xl mx-auto min-h-screen text-gray-900 antialiased font-sans">
    
    <!-- Header Section -->
    <header class="mb-12 md:mb-20">
        <h1 class="font-headline text-5xl md:text-6xl font-extrabold text-[#173124] tracking-tighter mb-4">The Living Archive</h1>
        <p class="font-body text-[#424844] max-w-2xl text-lg leading-relaxed">Join our community of conscious curators. Share your repair journey and document the life of your garments.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- Left Column: Community Feed -->
        <div class="lg:col-span-8 space-y-12">
            
            <!-- Create Post Trigger Card -->
            <div class="bg-white p-6 rounded-xl editorial-shadow border border-[#e2e3e0] flex items-center gap-4 transition-all hover:bg-[#eeeeeb] cursor-pointer group" onclick="openModal('createModal')">
                <div class="w-12 h-12 rounded-full bg-[#ccead6] overflow-hidden flex-shrink-0 flex items-center justify-center font-bold text-[#062014]">
                    <span>{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                </div>
                <button class="flex-grow text-left text-[#424844] font-medium py-3 px-6 rounded-full bg-[#eeeeeb] group-hover:bg-[#e2e3e0] transition-colors">
                    What's your sustainable story, {{ explode(' ', Auth::user()->name ?? 'Guest')[0] }}?
                </button>
                <button class="bg-[#173124] text-white px-6 py-3 rounded-full font-bold flex items-center gap-2 transition-all hover:opacity-90 active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Post
                </button>
            </div>

            <!-- Feed Items -->
            <div class="space-y-6">
                @forelse($posts as $post)
                <article class="relative bg-white rounded-xl overflow-hidden editorial-shadow border border-[#e2e3e0]">
                    <div class="flex items-center gap-4 m-6 mb-2">
                        <div class="w-10 h-10 rounded-full bg-[#ffdbcf] overflow-hidden flex-shrink-0 flex items-center justify-center font-bold text-[#380d00] border border-[#c2c8c2]">
                            {{ \Illuminate\Support\Str::substr($post->user->name ?? 'User', 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-headline font-bold text-[#173124] text-base leading-tight">{{ $post->user->name ?? 'Anonymous' }}</h3>
                            <p class="text-[10px] text-[#424844] font-medium uppercase tracking-widest mt-0.5">{{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <!-- Post Header Context Controls (Kebab Menu Anchor remains at the card top right) -->
                    @auth
                        @if(Auth::id() == $post->users_id || Auth::id() == 1) 
                        <div class="absolute right-4 top-4 z-20">
                            <button onclick="toggleDropdown({{ $post->post_id }})" class="kebab-button text-[#424844] hover:text-[#173124] font-bold text-xl px-2 pb-2 transition-colors">⋮</button>
                            
                            <div id="dropdown-{{ $post->post_id }}" class="dropdown-menu hidden absolute right-0 top-8 w-40 bg-white rounded-xl shadow-lg border border-[#e2e3e0] overflow-hidden z-30">
                                <button onclick="openEditModal(this)" 
                                        data-id="{{ $post->post_id }}" 
                                        data-title="{{ $post->title }}" 
                                        data-content="{{ $post->content }}" 
                                        class="w-full text-left px-4 py-3 text-sm text-[#1a1c1b] hover:bg-[#f4f4f1] font-semibold border-b border-[#eeeeeb] transition-colors">
                                    ✏️ Edit Post
                                </button>
                                <form action="{{ route('community.destroy', $post->post_id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full text-left px-4 py-3 text-sm text-[#ba1a1a] hover:bg-[#ffdad6] font-semibold transition-colors" onclick="return confirm('Are you sure you want to delete this post?')">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    @endauth

                    <!-- Optional Post Image Displayed First -->
                    @if($post->image_path)
                    <div class="w-full overflow-hidden bg-[#f4f4f1]">
                        <img src="{{ asset('storage/' . $post->image_path) }}" alt="Post image" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                    </div>
                    @endif
                    
                    <!-- Card Body Content Container -->
                    <div class="p-8">                        

                        <h2 class="font-headline text-2xl font-bold text-[#173124] mb-3">{{ $post->title }}</h2>
                        <p class="text-[#1a1c1b] leading-relaxed mb-6 whitespace-pre-line">{{ $post->content }}</p>
                        
                        <div class="flex items-center justify-between pt-6 border-t border-[#eeeeeb]">
                            @auth
                            @php
                                $currentVote = $post->my_vote ?? $myVote ?? 0; 
                            @endphp

                            <!-- using X data so that it didnt refresh when vote -->
                            <div x-data="{ 
                                    currentVote: {{ $currentVote }}, 
                                    score: {{ $post->upvote_count ?? 0 }},
                                    castVote(value) {
                                        let targetValue = value;
                                        
                                        // If user clicks the active button again, they are canceling their vote (toggle off)
                                        if (this.currentVote === value) {
                                            this.score -= value;
                                            this.currentVote = 0;
                                            targetValue = value === 1 ? -1 : 1; // Send inverse value or let backend know to toggle
                                        } else {
                                            // Adjust the local score dynamically based on the shift
                                            this.score += (this.currentVote === 0) ? value : (value * 2);
                                            this.currentVote = value;
                                        }

                                        // 2. Perform silent background communication to your controller
                                        fetch('{{ route('community.vote', $post->post_id) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ value: value })
                                        });
                                    }
                                }" 
                                class="flex items-center gap-1 bg-[#f4f4f1] rounded-full p-1">
                                
                                {{-- Upvote Action --}}
                                <button @click="castVote(1)"
                                        type="button"
                                        class="p-2 rounded-full transition-all flex items-center group"
                                        :class="currentVote === 1 ? 'bg-[#ccead6] text-[#062014] ring-2 ring-[#b0cdbb]' : 'hover:bg-[#ccead6] text-[#173124]'">
                                    <span class="material-symbols-outlined text-base">arrow_upward</span>
                                </button>

                                {{-- Dynamic Score Counter Display handled by Alpine x-text --}}
                                <span x-text="score"
                                    class="font-bold text-sm px-2 min-w-[2rem] text-center transition-colors"
                                    :class="currentVote === 1 ? 'text-[#062014]' : (currentVote === -1 ? 'text-[#ba1a1a]' : 'text-[#1a1c1b]')">
                                    {{ $post->upvote_count ?? 0 }}
                                </span>

                                {{-- Downvote Action --}}
                                <button @click="castVote(-1)"
                                        type="button"
                                        class="p-2 rounded-full transition-all flex items-center group"
                                        :class="currentVote === -1 ? 'bg-[#ffdad6] text-[#ba1a1a] ring-2 ring-[#ffdad6]' : 'hover:bg-[#ffdad6] text-[#424844] hover:text-[#ba1a1a]'">
                                    <span class="material-symbols-outlined text-base">arrow_downward</span>
                                </button>
                                
                            </div>
                        @else
                            {{-- Guest View State Container unchanged --}}
                            <div class="flex items-center gap-1.5 bg-[#f4f4f1] rounded-full px-3 py-2 text-[#424844] border border-[#eeeeeb]">
                                <span class="material-symbols-outlined text-sm text-[#424844] select-none">arrow_upward</span>
                                <span class="font-bold text-sm text-[#1a1c1b] px-0.5">{{ $post->upvote_count ?? 0 }}</span>
                                <span class="material-symbols-outlined text-sm text-[#424844] select-none">arrow_downward</span>
                            </div>
                        @endauth
                        </div>
                    </div>
                </article>
                @empty
                <!-- Empty State Container -->
                <div class="text-center py-20 bg-[#f4f4f1] rounded-xl border-2 border-dashed border-[#c2c8c2]">
                    <span class="material-symbols-outlined text-5xl text-[#424844] mb-3">eco</span>
                    <h3 class="font-headline font-bold text-[#1a1c1b] text-lg">The archive is empty</h3>
                    <p class="text-[#424844] text-sm mt-1">Be the first to share your sustainable story!</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <aside class="lg:col-span-4 space-y-12">
            <!-- Tip of the Day Card -->
            <section class="bg-[#fea181] text-[#380d00] p-8 rounded-xl editorial-shadow">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-[#924a2f]">lightbulb</span>
                    <h4 class="font-headline font-bold uppercase tracking-widest text-xs">Tip of the Day</h4>
                </div>
                <p class="font-headline text-xl font-bold leading-snug">The most sustainable garment is the one already in your closet.</p>
            </section>

            <!-- Leaderboard Card Mini Box -->
            <section class="bg-[#e2e3e0] p-8 rounded-xl">
                <h4 class="font-headline font-bold text-[#173124] mb-6 uppercase tracking-wider text-xs">Top Curators</h4>
                <div class="space-y-6 mb-6">
                    
                </div>
                <button class="w-full bg-white border border-[#c2c8c2] text-[10px] font-bold py-3 rounded-full hover:bg-[#f4f4f1] hover:text-[#173124] transition-all uppercase tracking-wider text-[#424844] shadow-sm">
                    View Leaderboard
                </button>
            </section>
        </aside>
    </div>

    <!-- Create Post Modal Container -->
    <div id="createModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-xl w-full max-w-xl p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 modal-box border border-[#e2e3e0]">
            <button onclick="closeModal('createModal')" class="absolute top-5 right-6 text-[#424844] hover:text-[#ba1a1a] text-xl font-bold transition-colors">✕</button>
            <h2 class="font-headline text-2xl font-bold mb-6 text-[#173124]">Create a Post</h2>
            
            <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                <input type="text" name="title" class="bg-[#f4f4f1] border border-[#e2e3e0] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#173124] focus:outline-none font-bold placeholder-[#424844]" placeholder="Give your story a title..." required>
                <textarea name="content" rows="4" class="bg-[#f4f4f1] border border-[#e2e3e0] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#173124] focus:outline-none resize-none placeholder-[#424844]" placeholder="Share your sustainable tips..." required></textarea>
                
                <div class="border-2 border-dashed border-[#b0cdbb] bg-[#ccead6]/20 rounded-xl p-6 text-center hover:bg-[#ccead6]/40 transition-colors cursor-pointer relative group mt-2">
                    <label class="cursor-pointer flex flex-col items-center gap-2 w-full h-full">
                        <span class="material-symbols-outlined text-3xl text-[#324c3e] group-hover:scale-110 transition-transform duration-300">photo_camera</span>
                        <span class="text-xs text-[#324c3e] font-semibold">Upload Image (Optional)</span>
                        <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this, 'createFileName')">
                    </label>
                    <p id="createFileName" class="text-xs text-[#062014] font-bold mt-3 hidden bg-[#ccead6] py-1 px-3 rounded-full inline-block"></p>
                </div>
                <button type="submit" class="bg-[#173124] text-white px-6 py-3.5 rounded-full text-sm font-bold hover:opacity-90 transition-opacity w-full mt-4">Share to Community</button>
            </form>
        </div>
    </div>

    <!-- Edit Post Modal Container -->
    <div id="editModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-xl w-full max-w-xl p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 modal-box border border-[#e2e3e0]">
            <button onclick="closeModal('editModal')" class="absolute top-5 right-6 text-[#424844] hover:text-[#ba1a1a] text-xl font-bold transition-colors">✕</button>
            <h2 class="font-headline text-2xl font-bold mb-6 text-[#173124]">Edit Post</h2>
            
            <form id="editForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf 
                @method('PUT')
                <input type="text" id="editTitle" name="title" class="bg-[#f4f4f1] border border-[#e2e3e0] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#173124] focus:outline-none font-bold text-[#1a1c1b]" required>
                <textarea id="editContent" name="content" rows="4" class="bg-[#f4f4f1] border border-[#e2e3e0] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#173124] focus:outline-none resize-none text-[#1a1c1b]" required></textarea>
                
                <div class="border-2 border-dashed border-[#b0cdbb] bg-[#ccead6]/20 rounded-xl p-6 text-center hover:bg-[#ccead6]/40 transition-colors cursor-pointer relative group mt-2">
                    <label class="cursor-pointer flex flex-col items-center gap-2 w-full h-full">
                        <span class="material-symbols-outlined text-3xl text-[#324c3e] group-hover:scale-110 transition-transform duration-300">photo_camera</span>
                        <span class="text-xs text-[#324c3e] font-semibold">Upload New Image (Optional)</span>
                        <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this, 'editFileName')">
                    </label>
                    <p id="editFileName" class="text-xs text-[#062014] font-bold mt-3 hidden bg-[#ccead6] py-1 px-3 rounded-full inline-block"></p>
                </div>
                <button type="submit" class="bg-[#173124] text-white px-6 py-3.5 rounded-full text-sm font-bold hover:opacity-90 transition-opacity w-full mt-4">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Interface Animation Scripts -->
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

        function openEditModal(btn) {
            const id = btn.getAttribute('data-id');
            document.getElementById('editTitle').value = btn.getAttribute('data-title');
            document.getElementById('editContent').value = btn.getAttribute('data-content');
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
            if (!event.target.matches('.kebab-button')) closeAllDropdowns();
            if (event.target.classList.contains('fixed')) closeModal(event.target.id);
        }

        function previewImage(input, textId) {
            if (input.files && input.files[0]) {
                const fileName = document.getElementById(textId);
                fileName.textContent = '✅ Selected: ' + input.files[0].name;
                fileName.classList.remove('hidden');
            }
        }

        
    </script>
</main>
@endsection