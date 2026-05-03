@extends('layouts.app')

@section('content')
<main class="pt-10 pb-24 px-6 max-w-7xl mx-auto">

    {{-- Back Link --}}
    <div class="mb-6">
        <a href="{{ route('items.show', $item) }}"
            class="group inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined transition-transform group-hover:-translate-x-1">arrow_back</span>
            Back to Listing
        </a>
    </div>

    {{-- Page Header --}}
    <header class="mb-12">
        <p class="text-secondary font-bold text-[11px] tracking-[0.2em] uppercase mb-1">Currently Editing</p>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter text-primary mb-3">Edit Listing</h1>
        <p class="text-on-surface-variant text-sm font-body">{{ $item->item_name }}</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

        {{-- ── Main Edit Form ────────────────────────────────────────── --}}
        <div class="lg:col-span-8 space-y-12">

            <form id="edit-form" action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <section class="bg-surface-container-low p-8 rounded-xl">
                    <h2 class="text-xl font-bold text-primary mb-1 flex items-center gap-2">
                        <span class="material-symbols-outlined">photo_library</span>
                        Visual Documentation
                    </h2>
                    <p class="text-xs text-on-surface-variant mb-6">Click any photo to replace it, or click the empty slots to add more.</p>

                    <input type="file" id="photo_input" accept="image/*" multiple class="hidden" />

                    <div id="existing-photos-container">
                        @php $photos = $item->photo_path ?? []; @endphp
                        @foreach($photos as $i => $path)
                            <input type="hidden" name="existing_photos[]" value="{{ $path }}" id="existing-{{ $i }}" />
                        @endforeach
                    </div>

                    <div id="photo-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4">

                        @php $photos = $item->photo_path ?? []; @endphp

                        @for ($i = 0; $i < 4; $i++)
                            @if(isset($photos[$i]))
                                {{-- Filled slot with existing photo --}}
                                <div id="slot-{{ $i }}"
                                    data-existing="{{ $photos[$i] }}"
                                    class="photo-slot aspect-square rounded-lg overflow-hidden relative group cursor-pointer"
                                    onclick="triggerUpload({{ $i }})">
                                    <img src="{{ asset('storage/' . $photos[$i]) }}"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                         alt="Photo {{ $i + 1 }}" />
                                    <div class="absolute inset-0 bg-primary/30 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-white text-2xl">edit</span>
                                        <button type="button"
                                            onclick="event.stopPropagation(); removeSlot({{ $i }})"
                                            class="bg-surface-container-lowest/90 px-2 py-1 rounded-full text-error flex items-center gap-1 text-xs font-bold shadow">
                                            <span class="material-symbols-outlined text-sm leading-none">delete</span> Remove
                                        </button>
                                    </div>
                                </div>
                            @elseif($i === 0 || isset($photos[$i - 1]))
                                {{-- Empty slot (primary add or sequential) --}}
                                <div id="slot-{{ $i }}"
                                    style="{{ $i > 0 && !isset($photos[$i - 1]) ? 'display:none' : '' }}"
                                    class="photo-slot aspect-square bg-surface-container-lowest rounded-lg border-2 border-dashed {{ $i === 0 ? 'border-outline-variant' : 'border-outline-variant/30' }} flex flex-col items-center justify-center text-on-surface-variant {{ $i === 0 ? '' : 'text-on-surface-variant/40' }} hover:border-primary hover:text-on-surface-variant transition-colors cursor-pointer group"
                                    onclick="triggerUpload({{ $i }})">
                                    @if($i === 0)
                                        <span class="material-symbols-outlined text-3xl mb-2 group-hover:scale-110 transition-transform">add_a_photo</span>
                                        <span class="text-xs font-label">Add Photos</span>
                                    @else
                                        <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">add</span>
                                    @endif
                                </div>
                            @else
                                <div id="slot-{{ $i }}" style="display:none"
                                    class="photo-slot aspect-square bg-surface-container-highest/50 rounded-lg border-2 border-dashed border-outline-variant/30 flex flex-col items-center justify-center text-on-surface-variant/40 hover:border-outline-variant hover:text-on-surface-variant transition-colors cursor-pointer group"
                                    onclick="triggerUpload({{ $i }})">
                                    <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">add</span>
                                </div>
                            @endif
                        @endfor

                    </div>

                    <p id="photo-error" class="text-xs text-error mt-3 hidden">At least one photo is required.</p>
                    @error('photos') <p class="text-xs text-error mt-3">{{ $message }}</p> @enderror
                    @error('photos.*') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror

                    <p class="mt-4 text-xs text-on-surface-variant italic">
                        Tip: Up to 4 photos. New uploads replace existing ones in the same slot.
                    </p>
                </section>

                <section class="space-y-8">

                    {{-- Item Name & Category --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label for="item_name" class="block text-sm font-bold text-primary font-label">Item Name</label>
                            <input
                                id="item_name" name="item_name" type="text"
                                value="{{ old('item_name', $item->item_name) }}"
                                class="w-full bg-surface-container-highest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/20 text-on-surface placeholder:text-on-surface-variant/50 @error('item_name') ring-2 ring-error @enderror"
                                placeholder="e.g., 1990s Oversized Linen Blazer"
                            />
                            @error('item_name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="category_id" class="block text-sm font-bold text-primary font-label">Category</label>
                            <select
                                id="category_id" name="category_id"
                                class="w-full bg-surface-container-highest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/20 text-on-surface appearance-none @error('category_id') ring-2 ring-error @enderror"
                            >
                                <option value="" disabled>Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-bold text-primary font-label">Description</label>
                        <textarea
                            id="description" name="description" rows="5"
                            class="w-full bg-surface-container-highest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/20 text-on-surface placeholder:text-on-surface-variant/50 @error('description') ring-2 ring-error @enderror"
                            placeholder="Tell the story of this piece. Mention fit, fabric, and any unique details..."
                        >{{ old('description', $item->description) }}</textarea>
                        @error('description') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Size --}}
                    <div class="space-y-2">
                        <label for="size" class="block text-sm font-bold text-primary font-label">Size</label>
                        <input
                            id="size" name="size" type="text"
                            value="{{ old('size', $item->size) }}"
                            class="w-full bg-surface-container-highest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/20 text-on-surface placeholder:text-on-surface-variant/50 @error('size') ring-2 ring-error @enderror"
                            placeholder="e.g., M, L, XL, 42"
                        />
                        @error('size') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Condition & Price --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <div class="space-y-2">
                            <label for="condition" class="block text-sm font-bold text-primary font-label">Condition</label>
                            <select
                                id="condition" name="condition"
                                class="w-full bg-surface-container-highest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/20 text-on-surface appearance-none @error('condition') ring-2 ring-error @enderror"
                            >
                                @php
                                    $conditions = [
                                        'new_with_tags' => 'New with Tags',
                                        'like_new'      => 'Like New',
                                        'good'          => 'Good',
                                        'fair'          => 'Fair',
                                    ];
                                @endphp
                                @foreach($conditions as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('condition', $item->condition) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('condition') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="price" class="block text-sm font-bold text-primary font-label">Price</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold select-none">Rp</span>
                                <input
                                    id="price" name="price" type="number"
                                    value="{{ old('price', $item->price) }}"
                                    class="w-full bg-surface-container-highest border-none rounded-lg p-4 pl-12 focus:ring-2 focus:ring-primary/20 text-on-surface @error('price') ring-2 ring-error @enderror"
                                    placeholder="0" min="0" step="1000"
                                />
                            </div>
                            @error('price') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </section>

                {{-- ── Action Buttons ────────────────────────────────── --}}
                <div class="pt-8 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <button
                        type="button"
                        id="submit-btn"
                        onclick="handleSubmit()"
                        class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-primary to-primary-container text-on-primary rounded-full font-bold text-base hover:shadow-lg hover:shadow-primary/10 transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Update Listing
                    </button>
                    <a href="{{ route('items.show', $item) }}"
                        class="w-full sm:w-auto px-12 py-4 bg-surface-container-high text-on-surface-variant rounded-full font-bold text-base hover:brightness-95 transition-all text-center">
                        Cancel
                    </a>
                </div>
                <p id="submit-error" class="text-xs text-error hidden mt-2">Please fix the errors above before submitting.</p>

            </form>

            {{-- ── Danger Zone ───────────────────────────────────────── --}}
            <section class="border border-error/20 rounded-xl p-8 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-widest text-error flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">warning</span>
                    Danger Zone
                </h3>
                <p class="text-sm text-on-surface-variant leading-relaxed">
                    Once you delete a listing, it cannot be recovered. All associated data and community likes will be permanently removed.
                </p>
                <form action="{{ route('items.destroy', $item) }}" method="POST"
                      onsubmit="return confirm('Are you sure? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-full border border-error/30 text-error font-bold text-sm hover:bg-error/5 transition-colors">
                        <span class="material-symbols-outlined text-base">delete_forever</span>
                        Delete Listing
                    </button>
                </form>
            </section>

        </div>

        <aside class="lg:col-span-4 sticky top-28 space-y-6">

            <div class="bg-surface-container-low rounded-xl overflow-hidden flex gap-0">
                <div class="w-32 shrink-0 aspect-[3/4] overflow-hidden relative group">
                    @if($item->first_photo)
                        <img src="{{ asset('storage/' . $item->first_photo) }}"
                             alt="{{ $item->item_name }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                    @else
                        <div class="w-full h-full bg-surface-container-highest flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant/30">image</span>
                        </div>
                    @endif
                    <div class="absolute top-3 left-3">
                        <span class="bg-surface-container-lowest/80 backdrop-blur-sm text-[10px] font-bold uppercase tracking-widest text-on-surface px-2 py-1 rounded-full">
                            Current Photo
                        </span>
                    </div>
                </div>
                <div class="p-4 flex flex-col justify-center">
                    <p class="text-[10px] text-on-surface-variant uppercase tracking-widest font-bold mb-1">Listed</p>
                    <p class="font-bold text-primary text-sm">{{ $item->created_at->format('d M Y') }}</p>
                </div>
            </div>

            <div class="bg-secondary-container/10 p-8 rounded-xl border border-secondary-container/20">
                <h3 class="text-lg font-bold text-on-secondary-container mb-4">Editing Tips</h3>
                <ul class="space-y-4 text-sm text-on-secondary-container/90">
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-sm pt-0.5">check_circle</span>
                        <span>Update photos if the item condition has changed since listing.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-sm pt-0.5">check_circle</span>
                        <span>Refreshing your description can re-attract buyers who previously passed.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-sm pt-0.5">check_circle</span>
                        <span>Lowering price by 10–15% can significantly speed up a sale.</span>
                    </li>
                </ul>
            </div>

        </aside>
    </div>
</main>

<script>
    const slotFiles    = [null, null, null, null];
    // Track whether a slot was explicitly cleared (so existing_photos[] is removed)
    const slotRemoved  = [false, false, false, false];
    let   activeSlot   = 0;

    function triggerUpload(slotIndex) {
        activeSlot = slotIndex;

        const old   = document.getElementById('photo_input');
        const fresh = document.createElement('input');
        fresh.type      = 'file';
        fresh.id        = 'photo_input';
        fresh.accept    = 'image/*';
        fresh.multiple  = slotIndex === 0;
        fresh.className = 'hidden';
        fresh.addEventListener('change', handlePhotoUpload);
        old.replaceWith(fresh);
        fresh.click();
    }

    function handlePhotoUpload(event) {
        const files = Array.from(event.target.files);
        if (!files.length) return;

        if (activeSlot === 0 && files.length > 1) {
            files.slice(0, 4).forEach((file, i) => {
                slotFiles[i]   = file;
                slotRemoved[i] = false;
                renderFilledSlot(i, URL.createObjectURL(file));
            });
        } else {
            slotFiles[activeSlot]   = files[0];
            slotRemoved[activeSlot] = false;
            renderFilledSlot(activeSlot, URL.createObjectURL(files[0]));
        }

        document.getElementById('photo-error').classList.add('hidden');
    }

    function renderFilledSlot(index, url) {
        const existingInput = document.getElementById('existing-' + index);
        if (existingInput) existingInput.remove();

        const slot = document.getElementById('slot-' + index);
        slot.style.display = '';
        slot.onclick       = () => triggerUpload(index);
        slot.className     = 'photo-slot aspect-square rounded-lg overflow-hidden relative group cursor-pointer';
        slot.innerHTML     = `
            <img src="${url}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Photo ${index + 1}" />
            <div class="absolute inset-0 bg-primary/30 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-white text-2xl">edit</span>
                <button type="button"
                    onclick="event.stopPropagation(); removeSlot(${index})"
                    class="bg-surface-container-lowest/90 px-2 py-1 rounded-full text-error flex items-center gap-1 text-xs font-bold shadow">
                    <span class="material-symbols-outlined text-sm leading-none">delete</span> Remove
                </button>
            </div>`;

        if (index + 1 < 4) {
            const next = document.getElementById('slot-' + (index + 1));
            if (next) next.style.display = '';
        }
    }

    function removeSlot(index) {
        const img = document.querySelector('#slot-' + index + ' img');
        if (img && img.src.startsWith('blob:')) URL.revokeObjectURL(img.src);

        slotFiles[index]   = null;
        slotRemoved[index] = true;

        // Remove the hidden existing-photo input from the container
        const existingInput = document.getElementById('existing-' + index);
        if (existingInput) existingInput.remove();

        const slot = document.getElementById('slot-' + index);

        if (index === 0) {
            slot.className = 'photo-slot aspect-square bg-surface-container-lowest rounded-lg border-2 border-dashed border-outline-variant flex flex-col items-center justify-center text-on-surface-variant hover:border-primary transition-colors cursor-pointer group';
            slot.onclick   = () => triggerUpload(0);
            slot.innerHTML = `
                <span class="material-symbols-outlined text-3xl mb-2 group-hover:scale-110 transition-transform">add_a_photo</span>
                <span class="text-xs font-label">Add Photos</span>`;
        } else {
            slot.className = 'photo-slot aspect-square bg-surface-container-highest/50 rounded-lg border-2 border-dashed border-outline-variant/30 flex flex-col items-center justify-center text-on-surface-variant/40 hover:border-outline-variant hover:text-on-surface-variant transition-colors cursor-pointer group';
            slot.onclick   = () => triggerUpload(index);
            slot.innerHTML = `<span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">add</span>`;
        }

        // Hide trailing empty slots
        for (let i = 1; i < 4; i++) {
            const prev = document.getElementById('slot-' + (i - 1));
            const curr = document.getElementById('slot-' + i);
            const prevHasContent = prev && prev.querySelector('img');
            if (!prevHasContent && !slotFiles[i]) {
                curr.style.display = 'none';
            }
        }
    }

    async function handleSubmit() {
        const btn       = document.getElementById('submit-btn');
        const photoErr  = document.getElementById('photo-error');
        const submitErr = document.getElementById('submit-error');

        const hasPhoto = slotFiles.some(Boolean) ||
                         document.querySelectorAll('input[name="existing_photos[]"]').length > 0;
        if (!hasPhoto) {
            photoErr.classList.remove('hidden');
            return;
        }
        photoErr.classList.add('hidden');
        submitErr.classList.add('hidden');

        btn.disabled    = true;
        btn.textContent = 'Saving…';

        const form    = document.getElementById('edit-form');
        const data    = new FormData(form);
        const postUrl = '{{ route("items.update", $item->id) }}';

        slotFiles.filter(Boolean).forEach(file => data.append('photos[]', file));

        try {
            const res = await fetch(postUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: data,
            });

            if (res.ok || res.redirected) {
                window.location.href = res.redirected ? res.url : '{{ route("items.show", $item->id) }}';
                return;
            }

            if (res.status === 422) {
                const json = await res.json();
                console.error('Validation errors:', json.errors);
                renderValidationErrors(json.errors ?? {});
                btn.disabled    = false;
                btn.textContent = 'Update Listing';
                submitErr.classList.remove('hidden');
                return;
            }

            throw new Error('Unexpected: ' + res.status);

        } catch (err) {
            console.error('Submit error:', err);
            btn.disabled      = false;
            btn.textContent   = 'Update Listing';
            submitErr.textContent = 'Something went wrong. Please try again.';
            submitErr.classList.remove('hidden');
        }
    }

    function renderValidationErrors(errors) {
        document.querySelectorAll('.js-error').forEach(el => el.remove());

        Object.entries(errors).forEach(([field, messages]) => {
            const input = field.startsWith('photos')
                ? document.getElementById('photo-error')
                : document.querySelector(`[name="${field}"]`);

            if (!input) return;

            if (field.startsWith('photos')) {
                input.textContent = messages[0];
                input.classList.remove('hidden');
            } else {
                const p = document.createElement('p');
                p.className   = 'text-xs text-error mt-1 js-error';
                p.textContent = messages[0];
                input.closest('.space-y-2')?.appendChild(p);
            }
        });
    }
</script>
@endsection