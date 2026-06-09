@extends('layouts.app')

@section('content')
<main class="pt-10 pb-20 px-6 max-w-screen-2xl mx-auto min-h-screen text-gray-900 antialiased font-sans">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <header class="mb-12 md:mb-5">
        <h1 class="font-headline text-5xl md:text-6xl font-extrabold text-[#173124] tracking-tighter mb-4">The Living Archive</h1>
        <p class="font-body text-[#424844] max-w-2xl text-lg leading-relaxed">Join our community of conscious curators. Share your repair journey and document the life of your garments.</p>
    </header>

    <div class="w-full bg-black rounded-xl overflow-hidden relative h-72 mb-12 shadow-lg hidden md:block border border-[#e2e3e0]">
        <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?q=80&w=1200&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-50" alt="Banner">
        <div class="absolute inset-0 p-10 flex flex-col justify-end z-10">
            <span class="bg-[#fea181] text-[#380d00] text-[10px] font-bold px-3 py-1 rounded-full w-max mb-3 uppercase tracking-widest">Story of the Week</span>
            <h2 class="font-headline text-3xl font-bold text-white mb-2">The Leather Legacy</h2>
            <button class="bg-white text-[#173124] font-bold text-sm px-6 py-2.5 rounded-full w-max hover:bg-[#eeeeeb] transition-colors shadow-sm">Read Story</button>
        </div>
    </div>

    {{-- System Status Notification Banners --}}
    @if(session('success'))
        <div class="mb-8 bg-[#ccead6]/40 border border-[#b0cdbb] text-[#062014] px-6 py-4 rounded-xl text-sm font-medium flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-[#173124]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="mb-8 bg-[#ffdad6] border border-[#ffdad6] text-[#ba1a1a] px-6 py-4 rounded-xl text-sm font-medium">
            <div class="flex items-center gap-2 font-bold mb-2">
                <span class="material-symbols-outlined text-base">error</span>
                <p>Wait, something went wrong:</p>
            </div>
            <ul class="list-disc pl-8 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Active Ongoing Challenges Carousel --}}
    @if(isset($activeChallenges) && $activeChallenges->isNotEmpty())
    <div class="mb-12">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold uppercase tracking-widest text-[#173124] font-headline flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">local_fire_department</span>
                Trending Challenges
            </h3>
            <a href="{{ route('challenges.index') }}" class="text-xs font-bold text-[#424844] hover:text-[#173124] transition-colors uppercase tracking-wider">View All</a>
        </div>
        
        <div class="flex gap-4 overflow-x-auto pb-4 snap-x" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($activeChallenges as $challenge)
                <a href="{{ route('challenges.show', $challenge->id) }}" class="snap-start flex-shrink-0 bg-[#173124] rounded-xl p-6 w-[300px] relative overflow-hidden group border border-[#e2e3e0]/10 shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ccead6 1px, transparent 1px); background-size: 20px 20px;"></div>
                    
                    <div class="relative z-10">
                        <span class="inline-block px-2.5 py-1 bg-[#ccead6]/20 text-[#ccead6] border border-[#b0cdbb]/30 rounded-lg text-[10px] font-bold uppercase tracking-wider mb-4 backdrop-blur-sm shadow-sm">
                            #{{ $challenge->hashtag }}
                        </span>
                        <h4 class="text-white font-extrabold text-xl font-headline mb-2 leading-tight">{{ $challenge->title }}</h4>
                        <p class="text-[#eeeeeb]/70 text-xs line-clamp-2 leading-relaxed">{{ $challenge->description }}</p>
                    </div>

                    <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-[100px] text-[#062014]/40 group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-500 select-none">style</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <div class="lg:col-span-8 space-y-8">
            
        @auth
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
        @endauth

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

                    @auth
                        @if(Auth::id() == $post->users_id || Auth::id() == 1) 
                        <div class="absolute right-4 top-4 z-20">
                            <button onclick="toggleDropdown({{ $post->post_id }})" class="kebab-button text-[#424844] hover:text-[#173124] font-bold text-xl px-2 pb-2 transition-colors">⋮</button>
                            
                            <div id="dropdown-{{ $post->post_id }}" class="dropdown-menu hidden absolute right-0 top-8 w-40 bg-white rounded-xl shadow-lg border border-[#e2e3e0] overflow-hidden z-30">
                                <button onclick="openEditModal(this)" 
                                        data-id="{{ $post->post_id }}" 
                                        data-title="{{ $post->title }}" 
                                        data-content="{{ $post->content }}" 
                                        data-tags="{{ is_array($post->tags) ? implode(', ', $post->tags) : (is_string($post->tags) ? $post->tags : '') }}"
                                        class="w-full text-left px-4 py-3 text-sm text-[#1a1c1b] hover:bg-[#f4f4f1] font-semibold border-b border-[#eeeeeb] transition-colors flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">edit</span> Edit Post
                                </button>
                                <form action="{{ route('community.destroy', $post->post_id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full text-left px-4 py-3 text-sm text-[#ba1a1a] hover:bg-[#ffdad6] font-semibold transition-colors flex items-center gap-2" onclick="return confirm('Are you sure you want to delete this post?')">
                                        <span class="material-symbols-outlined text-sm">delete</span> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    @endauth

                    @if($post->image_path)
                    <div class="w-full overflow-hidden bg-[#f4f4f1] border-b border-[#e2e3e0] max-h-[440px]">
                        <img src="{{ asset('storage/' . $post->image_path) }}" alt="Post image" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                    </div>
                    @endif
                    
                    <div class="p-8">        
                        <h2 class="font-headline text-2xl font-bold text-[#173124] mb-3">{{ $post->title }}</h2>
                        <p class="text-[#1a1c1b] leading-relaxed mb-4 whitespace-pre-line text-sm">{{ $post->content }}</p>
                        
                        {{-- Safe Tag Implementation Block Wrapper --}}
                        @if(!empty($post->tags))
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @php 
                                $rawTags = is_array($post->tags) 
                                    ? $post->tags 
                                    : (json_decode($post->tags, true) ?? explode(',', (string)$post->tags));
                                $tagsArray = is_array($rawTags) ? $rawTags : (array)$rawTags;
                            @endphp
                            @foreach($tagsArray as $tag)
                                @if(is_string($tag) || is_numeric($tag))
                                    @if(trim((string)$tag) !== '')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#ccead6]/40 text-[#173124] border border-[#b0cdbb]/30">
                                        #{{ trim((string)$tag) }}
                                    </span>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                        @endif
                        
                        <div class="flex items-center justify-between pt-6 border-t border-[#eeeeeb]">
                            @auth
                            @php
                                $currentVote = $post->my_vote ?? 0; 
                            @endphp

                            <div x-data="{ 
                                    currentVote: {{ $currentVote }}, 
                                    score: {{ $post->upvote_count ?? 0 }},
                                    castVote(value) {
                                        let targetValue = value;
                                        if (this.currentVote === value) {
                                            this.score -= value;
                                            this.currentVote = 0;
                                            targetValue = value === 1 ? -1 : 1;
                                        } else {
                                            this.score += (this.currentVote === 0) ? value : (value * 2);
                                            this.currentVote = value;
                                        }
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
                                class="flex items-center gap-1 bg-[#f4f4f1] rounded-full p-1 border border-[#e2e3e0]">
                                
                                <button @click="castVote(1)"
                                        type="button"
                                        class="p-2 rounded-full transition-all flex items-center group"
                                        :class="currentVote === 1 ? 'bg-[#ccead6] text-[#062014] ring-2 ring-[#b0cdbb]' : 'hover:bg-[#ccead6] text-[#173124]'">
                                    <span class="material-symbols-outlined text-base font-bold">arrow_upward</span>
                                </button>

                                <span x-text="score"
                                    class="font-bold text-xs px-2 min-w-[2rem] text-center transition-colors cursor-pointer"
                                    :class="currentVote === 1 ? 'text-[#062014]' : (currentVote === -1 ? 'text-[#ba1a1a]' : 'text-[#1a1c1b]')">
                                    {{ $post->upvote_count ?? 0 }}
                                </span>

                                <button @click="castVote(-1)"
                                        type="button"
                                        class="p-2 rounded-full transition-all flex items-center group"
                                        :class="currentVote === -1 ? 'bg-[#ffdad6] text-[#ba1a1a] ring-2 ring-[#ffdad6]' : 'hover:bg-[#ffdad6] text-[#424844] hover:text-[#ba1a1a]'">
                                    <span class="material-symbols-outlined text-base font-bold">arrow_downward</span>
                                </button>
                            </div>
                            @else
                            <div class="flex items-center gap-1.5 bg-[#f4f4f1] rounded-full px-3 py-2 text-[#424844] border border-[#eeeeeb]">
                                <span class="material-symbols-outlined text-sm text-[#424844] select-none">arrow_upward</span>
                                <span class="font-bold text-xs text-[#1a1c1b] px-0.5">{{ $post->upvote_count ?? 0 }}</span>
                                <span class="material-symbols-outlined text-sm text-[#424844] select-none">arrow_downward</span>
                            </div>
                            @endauth

                            @auth
                                @if(Auth::id() == $post->users_id || Auth::id() == 1)
                                <button onclick="openBreakdownModal({{ $post->post_id }})" class="text-[#424844] hover:text-[#173124] transition-colors text-xs font-bold flex items-center gap-1.5 uppercase tracking-wider bg-transparent border-none outline-none cursor-pointer">
                                    <span class="material-symbols-outlined text-sm">analytics</span> Analytics
                                </button>
                                @endif
                            @endauth

                            <span class="text-[#424844] hover:text-[#173124] transition-colors cursor-pointer text-xs font-bold flex items-center gap-1 uppercase tracking-wider">
                                <span class="material-symbols-outlined text-sm">share</span> Share
                            </span>
                        </div>
                    </div>
                </article>
                @empty
                <div class="text-center py-20 bg-[#f4f4f1] rounded-xl border-2 border-dashed border-[#c2c8c2]">
                    <span class="material-symbols-outlined text-5xl text-[#424844] mb-3 select-none">eco</span>
                    <h3 class="font-headline font-bold text-[#1a1c1b] text-lg">The archive is empty</h3>
                    <p class="text-[#424844] text-sm mt-1">Be the first to share your sustainable story!</p>
                </div>
                @endforelse
            </div>
        </div>

        <aside class="lg:col-span-4 space-y-12">
            <section class="bg-[#fea181] text-[#380d00] p-8 rounded-xl editorial-shadow">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-[#924a2f]">lightbulb</span>
                    <h4 class="font-headline font-bold uppercase tracking-widest text-xs">Tip of the Day</h4>
                </div>
                <p class="font-headline text-xl font-bold leading-snug">The most sustainable garment is the one already in your closet.</p>
            </section>

            <section class="bg-[#e2e3e0] p-8 rounded-xl border border-[#c2c8c2]/60">
                <h4 class="font-headline font-bold text-[#173124] mb-6 uppercase tracking-wider text-xs">Top Curators</h4>
                <div class="space-y-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-[#fea181] rounded-full flex items-center justify-center text-[#380d00] text-xs font-bold shadow-sm">1</div>
                        <p class="font-bold text-sm text-[#1a1c1b]">Julian Vogel</p>
                    </div>
                </div>
                <button class="w-full bg-white border border-[#c2c8c2] text-[10px] font-bold py-3 rounded-full hover:bg-[#f4f4f1] hover:text-[#173124] transition-all uppercase tracking-wider text-[#424844] shadow-sm">
                    View Leaderboard
                </button>
            </section>
        </aside>
    </div>

    <div id="createModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-xl w-full max-w-xl p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 modal-box border border-[#e2e3e0] max-h-[90vh] overflow-y-auto">
            <button type="button" onclick="closeModal('createModal')" class="absolute top-5 right-6 text-[#424844] hover:text-[#ba1a1a] text-xl font-bold transition-colors z-10">✕</button>
            <h2 class="font-headline text-2xl font-bold mb-6 text-[#173124]">Create a Post</h2>
            
            <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf
                <input type="text" name="title" class="bg-[#f4f4f1] border border-[#e2e3e0] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#173124] focus:outline-none font-bold placeholder-[#424844]" placeholder="Give your story a title..." required>
                <textarea name="content" rows="3" class="bg-[#f4f4f1] border border-[#e2e3e0] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#173124] focus:outline-none resize-none placeholder-[#424844]" placeholder="Share your sustainable tips..." required></textarea>
                
                {{-- Hashtag Challenge Interface Elements Blocks --}}
                <div class="bg-[#ccead6]/20 border border-[#b0cdbb]/40 rounded-xl p-4">
                    <label class="block text-xs font-bold text-[#173124] uppercase tracking-widest mb-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">tag</span> Challenge Tags
                    </label>

                    @if(isset($activeChallenges) && $activeChallenges->isNotEmpty())
                    <div class="mb-3">
                        <p class="text-[10px] text-[#424844] font-semibold uppercase tracking-widest mb-2">Active Challenges — click to add:</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($activeChallenges as $ac)
                            <button type="button"
                                onclick="addTag('createTags', '{{ $ac->hashtag }}', 'createTagPreview')"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#173124] text-white hover:bg-[#324c3e] transition-colors cursor-pointer shadow-sm">
                                <span class="material-symbols-outlined text-[10px]">military_tech</span> #{{ $ac->hashtag }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <input type="text" name="tags" id="createTags"
                           class="w-full bg-[#f4f4f1] border border-[#e2e3e0] rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[#173124] focus:outline-none placeholder-[#424844]"
                           placeholder="or type manually: rewear30days, slowfashion"
                           oninput="this.value=this.value.toLowerCase(); previewHashtags(this.value, 'createTagPreview')">
                    
                    <div id="createTagPreview" class="mt-2 space-y-1.5"></div>
                </div>

                {{-- Interactive Upload File Container Layout Block --}}
                <div class="border-2 border-dashed border-[#b0cdbb] bg-[#ccead6]/10 rounded-xl p-6 text-center hover:bg-[#ccead6]/20 transition-colors cursor-pointer relative group mt-1">
                    <label class="cursor-pointer flex flex-col items-center gap-2 w-full h-full">
                        <span class="material-symbols-outlined text-3xl text-[#324c3e] group-hover:scale-110 transition-transform duration-300">photo_camera</span>
                        <span class="text-xs text-[#324c3e] font-semibold">Upload Image (Optional)</span>
                        <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this, 'createFileName', 'createImagePreview')">
                    </label>
                    <p id="createFileName" class="text-xs text-[#062014] font-bold mt-3 hidden bg-[#ccead6] py-1 px-3 rounded-full inline-block"></p>
                    <img id="createImagePreview" class="hidden mt-4 w-full h-40 object-cover rounded-xl border border-[#b0cdbb] shadow-sm" alt="Preview element" />
                </div>
                <button type="submit" class="bg-[#173124] text-white px-6 py-3.5 rounded-full text-sm font-bold hover:opacity-90 transition-opacity w-full mt-2">Share to Community</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-xl w-full max-w-xl p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 modal-box border border-[#e2e3e0] max-h-[90vh] overflow-y-auto">
            <button type="button" onclick="closeModal('editModal')" class="absolute top-5 right-6 text-[#424844] hover:text-[#ba1a1a] text-xl font-bold transition-colors z-10">✕</button>
            <h2 class="font-headline text-2xl font-bold mb-6 text-[#173124]">Edit Post</h2>
            
            <form id="editForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                @csrf 
                @method('PUT')
                <input type="text" id="editTitle" name="title" class="bg-[#f4f4f1] border border-[#e2e3e0] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#173124] focus:outline-none font-bold text-[#1a1c1b]" required>
                <textarea id="editContent" name="content" rows="3" class="bg-[#f4f4f1] border border-[#e2e3e0] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#173124] focus:outline-none resize-none text-[#1a1c1b]" required></textarea>
                
                {{-- Dynamic Edit Modal Hashtag Tag Layout Block --}}
                <div class="bg-[#ccead6]/20 border border-[#b0cdbb]/40 rounded-xl p-4">
                    <label class="block text-xs font-bold text-[#173124] uppercase tracking-widest mb-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">tag</span> # Tags
                    </label>
                    <input type="text" name="tags" id="editTags"
                           class="w-full bg-white border border-[#e2e3e0] rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-[#173124] focus:outline-none lowercase placeholder-[#424844]"
                           placeholder="rewear30days, slowfashion"
                           oninput="previewHashtags(this.value, 'editTagPreview')">
                    <div id="editTagPreview" class="mt-2 space-y-1.5"></div>
                </div>

                {{-- Interactive Form Modification Image Management Block Container --}}
                <div class="border-2 border-dashed border-[#b0cdbb] bg-[#ccead6]/10 rounded-xl p-6 text-center hover:bg-[#ccead6]/20 transition-colors cursor-pointer relative group mt-1">
                    <label class="cursor-pointer flex flex-col items-center gap-2 w-full h-full">
                        <span class="material-symbols-outlined text-3xl text-[#324c3e] group-hover:scale-110 transition-transform duration-300">photo_camera</span>
                        <span class="text-xs text-[#324c3e] font-semibold">Upload New Image (Optional)</span>
                        <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this, 'editFileName', 'editImagePreview')">
                    </label>
                    <p id="editFileName" class="text-xs text-[#062014] font-bold mt-3 hidden bg-[#ccead6] py-1 px-3 rounded-full inline-block"></p>
                    <img id="editImagePreview" class="hidden mt-4 w-full h-40 object-cover rounded-xl border border-[#b0cdbb] shadow-sm" alt="Preview secondary element" />
                </div>
                <button type="submit" class="bg-[#173124] text-white px-6 py-3.5 rounded-full text-sm font-bold hover:opacity-90 transition-opacity w-full mt-2">Save Changes</button>
            </form>
        </div>
    </div>

    {{-- Post Analytics Display Sheet --}}
    <div id="breakdownModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-xl w-full max-w-sm p-6 shadow-2xl relative transform scale-95 transition-transform duration-300 modal-box text-center border border-[#e2e3e0]">
            <button type="button" onclick="closeModal('breakdownModal')" class="absolute top-5 right-6 text-[#424844] hover:text-[#ba1a1a] text-xl font-bold transition-colors z-10">✕</button>
            
            <h2 class="font-headline text-xl font-bold mb-1 text-[#173124]">Community Sentiment</h2>
            <p class="text-[10px] text-[#424844] font-bold mb-6 uppercase tracking-widest">Post Analytics Breakdown</p>

            <div id="breakdownLoading" class="py-6">
                <div class="w-8 h-8 border-4 border-[#173124] border-t-transparent rounded-full animate-spin mx-auto"></div>
            </div>

            <div id="breakdownContent" class="hidden grid grid-cols-2 gap-4 my-4">
                <div class="bg-[#ccead6]/30 border border-[#b0cdbb]/60 rounded-xl p-4 flex flex-col items-center">
                    <span class="material-symbols-outlined text-2xl mb-1 text-[#062014] select-none">thumb_up</span>
                    <span id="breakdownLikes" class="text-xl font-extrabold text-[#062014] block">0</span>
                    <span class="text-[10px] font-bold text-[#324c3e] uppercase tracking-wider mt-1">Likes</span>
                </div>
                <div class="bg-[#ffdad6]/60 border border-[#ffdad6] rounded-xl p-4 flex flex-col items-center">
                    <span class="material-symbols-outlined text-2xl mb-1 text-[#ba1a1a] select-none">thumb_down</span>
                    <span id="breakdownDislikes" class="text-xl font-extrabold text-[#ba1a1a] block">0</span>
                    <span class="text-[10px] font-bold text-[#ba1a1a] uppercase tracking-wider mt-1">Dislikes</span>
                </div>
            </div>
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

        async function openBreakdownModal(postId) {
            openModal('breakdownModal');
            
            const loadingNode = document.getElementById('breakdownLoading');
            const contentNode = document.getElementById('breakdownContent');
            const likesNode = document.getElementById('breakdownLikes');
            const dislikesNode = document.getElementById('breakdownDislikes');
            
            loadingNode.classList.remove('hidden');
            contentNode.classList.add('hidden');
            
            try {
                const response = await fetch(`/community/posts/${postId}/breakdown`);
                if(!response.ok) throw new Error('Data breakdown network failure execution context');
                const data = await response.json();
                
                likesNode.textContent = data.likes ?? 0;
                dislikesNode.textContent = data.dislikes ?? 0;
                
                loadingNode.classList.add('hidden');
                contentNode.classList.remove('hidden');
            } catch(e) {
                likesNode.textContent = '-';
                dislikesNode.textContent = '-';
                loadingNode.classList.add('hidden');
                contentNode.classList.remove('hidden');
            }
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
                        if (!r.ok) throw new Error('Network context lookup fault execution string response');
                        const d = await r.json();
                        lookupCache[tag] = d.challenge;
                        return { tag, challenge: d.challenge };
                    }));

                    preview.innerHTML = results.map(({ tag, challenge }) => challenge
                        ? `<div class="flex items-center gap-2 bg-[#ccead6]/40 border border-[#b0cdbb]/60 rounded-lg px-3 py-1.5">
                            <span class="text-[#173124] font-mono text-xs font-bold">#${tag}</span>
                            <span class="material-symbols-outlined text-xs text-[#424844]">trending_flat</span>
                            <span class="text-[#062014] font-bold text-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">military_tech</span> ${challenge.title}
                            </span>
                           </div>`
                        : `<div class="flex items-center gap-2 bg-[#f4f4f1] border border-[#e2e3e0] rounded-lg px-3 py-1.5">
                            <span class="text-[#424844] font-mono text-xs">#${tag}</span>
                            <span class="text-[#424844]/60 text-xs italic flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">info</span> no active challenge
                            </span>
                           </div>`
                    ).join('');
                } catch (error) {
                    preview.innerHTML = tags.map(tag => 
                        `<div class="flex items-center gap-2 bg-[#f4f4f1] border border-[#e2e3e0] rounded-lg px-3 py-1.5">
                            <span class="text-[#424844] font-mono text-xs">#${tag}</span>
                        </div>`
                    ).join('');
                }
            }, 300);
        }
    </script>
</main>
@endsection