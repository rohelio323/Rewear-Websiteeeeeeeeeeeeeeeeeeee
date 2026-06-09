@extends('layouts.admin')
@section('title', 'CO2 Categories')

@section('content')
{{-- Alpine wrapper for modal states and flash message auto-dismiss --}}
<div x-data="{ 
        showAddModal: false, 
        showEditModal: false, 
        editId: '', 
        editName: '', 
        editCo2: '',
        editRefNote: '',
        editRefUrl: '',
        showSuccess: true
    }" class="font-body max-w-5xl mx-auto pb-24">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-1 font-label">Administration</p>
            <h1 class="text-3xl font-extrabold text-emerald-950 tracking-tight font-headline">CO₂ Categories</h1>
            <p class="text-sm text-stone-500 mt-1">Manage clothing categories and their environmental impact metrics.</p>
        </div>
        
        <div class="flex-shrink-0">
            <button @click="showAddModal = true" class="flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-950 hover:bg-emerald-800 text-white rounded-xl text-sm font-bold transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 active:scale-95 active:translate-y-0">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Add Category
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-show="showSuccess" x-init="setTimeout(() => showSuccess = false, 4000)" x-transition.duration.500ms class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </div>
                <p class="text-sm font-bold text-emerald-800 font-headline">{{ session('success') }}</p>
            </div>
            <button @click="showSuccess = false" class="text-emerald-600 hover:text-emerald-800 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-start gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0 mt-0.5">
                <span class="material-symbols-outlined text-[18px]">error</span>
            </div>
            <div>
                <h3 class="text-sm font-bold text-red-800 mb-1 font-headline">Please review the following errors:</h3>
                <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Data Table --}}
    {{-- Note: Removed overflow-hidden to allow tooltips to break out of the container --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm relative">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50/80">
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label rounded-tl-2xl">Category Details</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">Impact (CO₂ Saved)</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label text-right rounded-tr-2xl">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($categories as $category)
                <tr class="hover:bg-stone-50 transition-colors group/row">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-stone-100 flex items-center justify-center text-stone-500 group-hover/row:bg-emerald-50 group-hover/row:text-emerald-600 transition-colors shrink-0">
                                <span class="material-symbols-outlined text-[20px]">checkroom</span>
                            </div>
                            <span class="font-bold text-stone-900 font-headline text-base">{{ $category->category_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100/50 font-mono text-xs font-bold shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-emerald-500">eco</span>
                                {{ number_format($category->co2_constant ?? 0, 2) }} kg
                            </span>
                            
                            {{-- Interactive Tooltip --}}
                            @if($category->reference_note || $category->reference_url)
                            <div x-data="{ showSource: false }" 
                                 @mouseenter="showSource = true" 
                                 @mouseleave="showSource = false" 
                                 class="relative flex items-center justify-center cursor-help">
                                
                                <span class="material-symbols-outlined text-[18px] text-stone-400 hover:text-emerald-600 transition-colors">help</span>
                                
                                {{-- The Tooltip Popup (with pb-3 for the invisible hover bridge) --}}
                                <div x-show="showSource" 
                                     x-transition.opacity.duration.200ms 
                                     style="display: none;" 
                                     class="absolute z-50 w-72 pb-3 bottom-full left-1/2 -translate-x-1/2">
                                    
                                    <div class="p-4 text-xs bg-stone-900 text-stone-100 rounded-2xl shadow-xl border border-stone-700 whitespace-normal relative">
                                        <p class="leading-relaxed text-stone-300 {{ $category->reference_url ? 'mb-3' : '' }}">
                                            {{ $category->reference_note }}
                                        </p>
                                        
                                        @if($category->reference_url)
                                            <a href="{{ $category->reference_url }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 hover:underline font-bold transition-colors">
                                                Source Link <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                            </a>
                                        @endif
                                        
                                        {{-- CSS Triangle pointing down --}}
                                        <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-stone-900 border-b border-r border-stone-700 transform rotate-45"></div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            
                            {{-- Edit Button --}}
                            <div class="relative group flex items-center justify-center">
                                <button type="button" 
                                        @click="showEditModal = true; 
                                                editId = '{{ $category->id }}'; 
                                                editName = '{{ addslashes($category->category_name) }}'; 
                                                editCo2 = '{{ $category->co2_constant }}';
                                                editRefNote = '{{ addslashes($category->reference_note ?? '') }}';
                                                editRefUrl = '{{ addslashes($category->reference_url ?? '') }}';" 
                                        class="p-2 rounded-lg text-stone-400 hover:bg-amber-50 hover:text-amber-600 transition-colors focus:outline-none">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                            </div>

                            {{-- Delete Form --}}
                            <div class="relative group flex items-center justify-center">
                                <form action="{{ url('/admin/categories/' . $category->id) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($category->category_name) }}? This action cannot be undone.');" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-stone-400 hover:bg-red-50 hover:text-red-600 transition-colors focus:outline-none">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-24 text-center text-stone-400 font-body">
                        <div class="w-20 h-20 mx-auto bg-stone-50 rounded-full flex items-center justify-center mb-4 border border-stone-100 shadow-inner">
                            <span class="material-symbols-outlined text-4xl text-stone-300">category</span>
                        </div>
                        <p class="font-bold text-stone-600 text-base mb-1">No categories configured</p>
                        <p class="text-sm text-stone-400 max-w-sm mx-auto mb-4">Set up categories and their associated CO₂ savings to start tracking environmental impact.</p>
                        <button @click="showAddModal = true" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors">
                            + Create your first category
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= Add Category Modal ================= --}}
    <div x-show="showAddModal" style="display:none;" class="relative z-50">
        <div x-show="showAddModal" x-transition.opacity.duration.300ms class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="showAddModal" 
                     @click.away="showAddModal = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl border border-stone-200 transform transition-all">
                    
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center shrink-0 border border-emerald-100">
                            <span class="material-symbols-outlined text-emerald-600 text-[24px]">add_circle</span>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-stone-900 text-xl font-headline leading-tight">Add Category</h2>
                            <p class="text-xs text-stone-500 mt-0.5">Define a new item type and its impact.</p>
                        </div>
                    </div>
                    
                    <form action="{{ url('/admin/categories') }}" method="POST">
                        @csrf
                        <div class="mb-5">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">Category Name</label>
                            <input type="text" name="category_name" required placeholder="e.g., Winter Coats, Denim" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 placeholder-stone-400 bg-stone-50 focus:bg-white">
                        </div>
                        
                        <div class="mb-5">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">CO₂ Saved (kg per item)</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-[18px]">eco</span>
                                <input type="number" step="0.01" name="co2_constant" required placeholder="e.g., 15.50" class="w-full pl-11 pr-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 placeholder-stone-400 bg-stone-50 focus:bg-white">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-1 font-label">Scientific Reference Note</label>
                            <textarea name="reference_note" rows="2" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 bg-stone-50 focus:bg-white resize-none" placeholder="e.g., Based on WRAP UK research..."></textarea>
                        </div>
                        <div class="mb-8">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-1 font-label">Source URL</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-stone-400 text-[18px]">link</span>
                                </div>
                                <input type="url" name="reference_url" class="w-full pl-9 pr-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 bg-stone-50 focus:bg-white" placeholder="https://wrap.org.uk/resources...">
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-stone-600 hover:bg-stone-100 transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-950 hover:bg-emerald-800 text-white text-sm font-bold transition shadow-md active:scale-95">Save Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Edit Category Modal ================= --}}
    <div x-show="showEditModal" style="display:none;" class="relative z-50">
        <div x-show="showEditModal" x-transition.opacity.duration.300ms class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="showEditModal" 
                     @click.away="showEditModal = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl border border-stone-200 transform transition-all">
                    
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center shrink-0 border border-amber-100">
                            <span class="material-symbols-outlined text-amber-600 text-[24px]">edit_document</span>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-stone-900 text-xl font-headline leading-tight">Edit Impact Value</h2>
                            <p class="text-xs text-stone-500 mt-0.5">Updating <span x-text="editName" class="font-bold text-stone-700"></span></p>
                        </div>
                    </div>
                    
                    <form :action="'{{ url('/admin/categories') }}/' + editId + '/co2-constant'" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-5">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">Category Name (Locked)</label>
                            <input type="text" x-model="editName" name="category_name" readonly class="w-full px-4 py-3 bg-stone-100 border border-stone-200 rounded-xl text-sm text-stone-500 cursor-not-allowed select-none">
                        </div>
                        
                        <div class="mb-5">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">CO₂ Saved (kg per item)</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600 text-[18px]">eco</span>
                                <input type="number" step="0.01" x-model="editCo2" name="co2_constant" required class="w-full pl-11 pr-4 py-3 border border-emerald-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-900 font-mono font-bold bg-emerald-50/30">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-1 font-label">Scientific Reference Note</label>
                            <textarea x-model="editRefNote" name="reference_note" rows="2" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 bg-stone-50 focus:bg-white resize-none"></textarea>
                        </div>
                        <div class="mb-8">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-1 font-label">Source URL</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-stone-400 text-[18px]">link</span>
                                </div>
                                <input type="url" x-model="editRefUrl" name="reference_url" class="w-full pl-9 pr-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 bg-stone-50 focus:bg-white">
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-stone-600 hover:bg-stone-100 transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition shadow-md active:scale-95 shadow-emerald-200">Update Value</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection