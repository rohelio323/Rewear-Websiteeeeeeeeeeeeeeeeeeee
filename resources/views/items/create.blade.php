@extends('layouts.app')

@section('content')
<main class="pt-10 pb-20 px-6 max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('marketplace.index') }}" 
            class="group inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined transition-transform group-hover:-translate-x-1">arrow_back</span>
            Back to Marketplace
        </a>
    </div>
    <header class="mb-12">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter text-primary mb-4">The Living Archive</h1>
        <p class="text-on-surface-variant max-w-xl font-body leading-relaxed">
            Pass forward your story. Every listed item extends the life of a garment and reduces our collective footprint.
        </p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

        {{-- Main Listing Form --}}
        <div class="lg:col-span-8 space-y-12">

            <form id="listing-form" action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- ── Image Upload ─────────────────────────────────────── --}}
                <section class="bg-surface-container-low p-8 rounded-xl mb-12">
                    <h2 class="text-xl font-bold text-primary mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined">photo_library</span>
                        Visual Documentation
                    </h2>

                    {{-- Hidden picker — replaced fresh on every click so same file re-triggers change --}}
                    <input type="file" id="photo_input" accept="image/*" multiple class="hidden" />

                    <div id="photo-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4">

                        <div id="slot-0"
                            class="photo-slot aspect-square bg-surface-container-lowest rounded-lg border-2 border-dashed border-outline-variant flex flex-col items-center justify-center text-on-surface-variant hover:border-primary transition-colors cursor-pointer group"
                            onclick="triggerUpload(0)">
                            <span class="material-symbols-outlined text-3xl mb-2 group-hover:scale-110 transition-transform">add_a_photo</span>
                            <span class="text-xs font-label">Add Photos</span>
                        </div>

                        <div id="slot-1" style="display:none"
                            class="photo-slot aspect-square bg-surface-container-highest/50 rounded-lg border-2 border-dashed border-outline-variant/30 flex flex-col items-center justify-center text-on-surface-variant/40 hover:border-outline-variant hover:text-on-surface-variant transition-colors cursor-pointer group"
                            onclick="triggerUpload(1)">
                            <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">add</span>
                        </div>

                        <div id="slot-2" style="display:none"
                            class="photo-slot aspect-square bg-surface-container-highest/50 rounded-lg border-2 border-dashed border-outline-variant/30 flex flex-col items-center justify-center text-on-surface-variant/40 hover:border-outline-variant hover:text-on-surface-variant transition-colors cursor-pointer group"
                            onclick="triggerUpload(2)">
                            <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">add</span>
                        </div>

                        <div id="slot-3" style="display:none"
                            class="photo-slot aspect-square bg-surface-container-highest/50 rounded-lg border-2 border-dashed border-outline-variant/30 flex flex-col items-center justify-center text-on-surface-variant/40 hover:border-outline-variant hover:text-on-surface-variant transition-colors cursor-pointer group"
                            onclick="triggerUpload(3)">
                            <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">add</span>
                        </div>

                    </div>

                    <p id="photo-error" class="text-xs text-error mt-3 hidden">Please add at least one photo.</p>

                    @error('photos')
                        <p class="text-xs text-error mt-3">{{ $message }}</p>
                    @enderror
                    @error('photos.*')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror

                    <p class="mt-4 text-xs text-on-surface-variant italic">
                        Tip: Use natural lighting to capture the garment's true character. Up to 4 photos.
                    </p>
                </section>

                {{-- ── Form Fields ───────────────────────────────────────── --}}
                <section class="space-y-8">

                    {{-- Item Name & Category --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label for="item_name" class="block text-sm font-bold text-primary font-label">Item Name</label>
                            <input
                                id="item_name" name="item_name" type="text"
                                value="{{ old('item_name') }}"
                                class="w-full bg-surface-container-highest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/20 text-on-surface placeholder:text-on-surface-variant/50 @error('item_name') ring-2 ring-error @enderror"
                                placeholder="e.g., 1990s Oversized Linen Blazer"
                            />
                            @error('item_name')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="category_id" class="block text-sm font-bold text-primary font-label">Category</label>
                            <select
                                id="category_id" name="category_id"
                                class="w-full bg-surface-container-highest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/20 text-on-surface appearance-none @error('category_id') ring-2 ring-error @enderror"
                            >
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-bold text-primary font-label">Description</label>
                        <textarea
                            id="description" name="description" rows="5"
                            class="w-full bg-surface-container-highest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/20 text-on-surface placeholder:text-on-surface-variant/50 @error('description') ring-2 ring-error @enderror"
                            placeholder="Tell the story of this piece. Mention fit, fabric, and any unique details..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Size --}}
                    <div class="space-y-2">
                        <label for="size" class="block text-sm font-bold text-primary font-label">Size</label>
                        <input
                            id="size" name="size" type="text"
                            value="{{ old('size') }}"
                            class="w-full bg-surface-container-highest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/20 text-on-surface placeholder:text-on-surface-variant/50 @error('size') ring-2 ring-error @enderror"
                            placeholder="e.g., M, L, XL, 42"
                        />
                        @error('size')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
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
                                    <option value="{{ $value }}" {{ old('condition') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('condition')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="price" class="block text-sm font-bold text-primary font-label">Price</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold select-none">Rp</span>
                                <input
                                    id="price" name="price" type="number"
                                    value="{{ old('price') }}"
                                    class="w-full bg-surface-container-highest border-none rounded-lg p-4 pl-12 focus:ring-2 focus:ring-primary/20 text-on-surface @error('price') ring-2 ring-error @enderror"
                                    placeholder="0" min="0" step="1000"
                                />
                            </div>
                            @error('price')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </section>

                <div class="pt-8 flex flex-col items-start gap-3">
                    <button
                        type="button"
                        id="submit-btn"
                        onclick="handleSubmit()"
                        class="w-full md:w-auto px-12 py-4 bg-gradient-to-r from-primary to-primary-container text-on-primary rounded-full font-bold text-lg hover:shadow-lg hover:shadow-primary/10 transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Confirm Listing
                    </button>
                    <p id="submit-error" class="text-xs text-error hidden">Please fix the errors above before submitting.</p>
                </div>

            </form>
        </div>

        {{-- ── Sidebar ───────────────────────────────────────────────── --}}
        <aside class="lg:col-span-4 sticky top-28 space-y-6">
            <div class="bg-secondary-container/10 p-8 rounded-xl border border-secondary-container/20">
                <h3 class="text-lg font-bold text-on-secondary-container mb-4">Archivist's Tips</h3>
                <ul class="space-y-4 text-sm text-on-secondary-container/90">
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-sm pt-0.5">check_circle</span>
                        <span>List items with high-quality, clear photos for 3x faster sales.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-sm pt-0.5">check_circle</span>
                        <span>Be honest about the condition to build trust with the community.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-sm pt-0.5">check_circle</span>
                        <span>Items listed at 'Fair' prices tend to find new homes in 48 hours.</span>
                    </li>
                </ul>
            </div>

        </aside>
    </div>

</main>

<script>
    
    const slotFiles = [null, null, null, null];
    let   activeSlot = 0;

    // Trigger file picker
    function triggerUpload(slotIndex) {
        activeSlot = slotIndex;

        // Replace the element entirely so .change fires even for the same file
        const old   = document.getElementById('photo_input');
        const fresh = document.createElement('input');
        fresh.type    = 'file';
        fresh.id      = 'photo_input';
        fresh.accept  = 'image/*';
        fresh.multiple = true;
        fresh.className = 'hidden';
        fresh.addEventListener('change', handlePhotoUpload);
        old.replaceWith(fresh);
        fresh.click();
    }

    // Handle selection
    function handlePhotoUpload(event) {
        const files = Array.from(event.target.files);
        if (!files.length) return;

        if (activeSlot === 0 && files.length > 1) {
            // Multi-select from slot 0: fill sequentially
            files.slice(0, 4).forEach((file, i) => {
                slotFiles[i] = file;
                renderSlot(i, file);
            });
        } else {
            slotFiles[activeSlot] = files[0];
            renderSlot(activeSlot, files[0]);
        }

        document.getElementById('photo-error').classList.add('hidden');
    }

    // Render a filled slot
    function renderSlot(index, file) {
        const slot = document.getElementById('slot-' + index);
        const url  = URL.createObjectURL(file);

        slot.style.display = '';
        slot.onclick       = null;
        slot.className     = 'photo-slot aspect-square rounded-lg overflow-hidden relative group';
        slot.innerHTML     = `
            <img src="${url}" class="w-full h-full object-cover" alt="Photo ${index + 1}" />
            <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <button type="button"
                    onclick="event.stopPropagation(); removeSlot(${index})"
                    class="bg-surface-container-lowest p-2 rounded-full text-error flex items-center justify-center shadow">
                    <span class="material-symbols-outlined text-base leading-none">delete</span>
                </button>
            </div>`;

        // Reveal the next empty slot
        if (index + 1 < 4 && !slotFiles[index + 1]) {
            document.getElementById('slot-' + (index + 1)).style.display = '';
        }
    }

    // Remove a slot
    function removeSlot(index) {
        const img = document.querySelector('#slot-' + index + ' img');
        if (img) URL.revokeObjectURL(img.src);

        slotFiles[index] = null;

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

        // Hide every slot whose predecessor is also empty
        for (let i = 1; i < 4; i++) {
            if (!slotFiles[i] && !slotFiles[i - 1]) {
                document.getElementById('slot-' + i).style.display = 'none';
            }
        }
    }

    // Submit via fetch so File objects are sent reliably
    async function handleSubmit() {
        const btn      = document.getElementById('submit-btn');
        const photoErr = document.getElementById('photo-error');
        const submitErr = document.getElementById('submit-error');
        const photos   = slotFiles.filter(Boolean);

        // Client-side photo guard
        if (photos.length === 0) {
            photoErr.classList.remove('hidden');
            return;
        }
        photoErr.classList.add('hidden');
        submitErr.classList.add('hidden');

        btn.disabled    = true;
        btn.textContent = 'Saving…';

        // Build FormData from the form fields
        const form = document.getElementById('listing-form');
        const data = new FormData(form);

        // Append File objects directly — this is what actually gets submitted
        photos.forEach(file => data.append('photos[]', file));

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json', // ask Laravel to return JSON on validation error
                },
                body: data,
            });

            if (res.ok || res.redirected) {
                // Success — follow the redirect Laravel sends back
                window.location.href = res.redirected ? res.url : '{{ route("marketplace.index") }}';
                return;
            }

            if (res.status === 422) {
                const json = await res.json();
                renderValidationErrors(json.errors ?? {});
                btn.disabled    = false;
                btn.textContent = 'Confirm Listing';
                submitErr.classList.remove('hidden');
                return;
            }

            throw new Error('Unexpected response: ' + res.status);

        } catch (err) {
            console.error(err);
            btn.disabled    = false;
            btn.textContent = 'Confirm Listing';
            submitErr.textContent = 'Something went wrong. Please try again.';
            submitErr.classList.remove('hidden');
        }
    }

    // Render server-side validation errors
    function renderValidationErrors(errors) {
        // Clear old dynamic errors
        document.querySelectorAll('.js-error').forEach(el => el.remove());

        Object.entries(errors).forEach(([field, messages]) => {
            // photos[] errors key is "photos" or "photos.0" etc.
            const name  = field.startsWith('photos') ? 'photos-error-area' : field;
            const input = field.startsWith('photos')
                ? document.getElementById('photo-error')
                : document.querySelector(`[name="${field}"]`);

            if (!input) return;

            const p = document.createElement('p');
            p.className   = 'text-xs text-error mt-1 js-error';
            p.textContent = messages[0];

            if (field.startsWith('photos')) {
                input.textContent = messages[0];
                input.classList.remove('hidden');
            } else {
                input.closest('.space-y-2')?.appendChild(p);
            }
        });
    }
</script>
@endsection