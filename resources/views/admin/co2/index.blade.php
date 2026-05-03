@extends('layouts.admin')
@section('title', 'CO2 Categories')

@section('content')
{{-- Wrap everything in Alpine to manage modal states --}}
<div x-data="{ 
        showAddModal: false, 
        showEditModal: false, 
        editId: '', 
        editName: '', 
        editCo2: '' 
    }" class="font-body max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-1 font-label">Administration</p>
            <h1 class="text-3xl font-extrabold text-emerald-950 tracking-tight font-headline">CO₂ Categories</h1>
            <p class="text-sm text-stone-500 mt-1">Manage clothing categories and their associated environmental impact values.</p>
        </div>
        
        <div class="flex-shrink-0">
            {{-- Add Button triggers the Add Modal --}}
            <button @click="showAddModal = true" class="flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-950 hover:bg-emerald-800 text-white rounded-xl text-sm font-bold transition-all shadow-sm hover:shadow active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Add Category
            </button>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <span class="material-symbols-outlined text-red-500 shrink-0">error</span>
            <div>
                <h3 class="text-sm font-bold text-red-800 mb-1 font-headline">Please fix the following errors:</h3>
                <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">Category Name</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">CO₂ Saved (per item)</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-stone-50/80 transition-colors group">
                        <td class="px-6 py-4 font-bold text-stone-900 font-headline">
                            {{ $category->category_name }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-emerald-50 text-emerald-700 border border-emerald-100/50 font-mono text-xs font-bold">
                                <span class="material-symbols-outlined text-[14px]">eco</span>
                                {{ number_format($category->co2_constant ?? 0, 2) }} kg
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Edit Button --}}
                                <button type="button" 
                                        @click="showEditModal = true; editId = '{{ $category->id }}'; editName = '{{ addslashes($category->category_name) }}'; editCo2 = '{{ $category->co2_constant }}'" 
                                        class="p-1.5 rounded-lg text-stone-400 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Edit CO2 Value">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>

                                {{-- Delete Form --}}
                                <form action="{{ url('/admin/categories/' . $category->id) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($category->category_name) }}? This action cannot be undone.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-stone-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Delete Category">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-20 text-center text-stone-400 text-sm font-body">
                            <div class="w-16 h-16 mx-auto bg-stone-50 rounded-full flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-3xl text-stone-300">category</span>
                            </div>
                            <p class="font-medium text-stone-500">No categories found.</p>
                            <p class="text-xs mt-1">Click "Add Category" to create your first one.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= Add Category Modal ================= --}}
    <div x-show="showAddModal" style="display:none;" x-transition.opacity>
        {{-- Modal Backdrop --}}
        <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            {{-- Modal Box --}}
            <div @click.away="showAddModal = false" class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl border border-stone-200 transform transition-all">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-emerald-600">add_circle</span>
                    </div>
                    <div>
                        <h2 class="font-bold text-stone-900 text-lg font-headline leading-tight">Add New Category</h2>
                        <p class="text-xs text-stone-500">Define the environmental impact.</p>
                    </div>
                </div>
                
                <form action="{{ url('/admin/categories') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">Category Name</label>
                        <input type="text" name="category_name" required placeholder="e.g., Winter Coats" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-stone-800 placeholder-stone-400">
                    </div>
                    
                    <div class="mb-8">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">CO₂ Saved (kg)</label>
                        <input type="number" step="0.01" name="co2_constant" required placeholder="e.g., 15.50" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-stone-800 placeholder-stone-400">
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-stone-500 hover:bg-stone-100 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition shadow-sm shadow-emerald-200">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= Edit Category Modal ================= --}}
    <div x-show="showEditModal" style="display:none;" x-transition.opacity>
        {{-- Modal Backdrop --}}
        <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            {{-- Modal Box --}}
            <div @click.away="showEditModal = false" class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl border border-stone-200 transform transition-all">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-amber-600">edit</span>
                    </div>
                    <div>
                        <h2 class="font-bold text-stone-900 text-lg font-headline leading-tight">Edit CO₂ Value</h2>
                        <p class="text-xs text-stone-500">Update the impact constant for <span x-text="editName" class="font-bold text-stone-700"></span>.</p>
                    </div>
                </div>
                
                {{-- Alpine binds the form action dynamically to the correct ID --}}
                <form :action="'{{ url('/admin/categories') }}/' + editId + '/co2-constant'" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">Category Name</label>
                        <input type="text" x-model="editName" name="category_name" readonly class="w-full px-4 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm text-stone-500 cursor-not-allowed">
                    </div>
                    
                    <div class="mb-8">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">CO₂ Saved (kg)</label>
                        <input type="number" step="0.01" x-model="editCo2" name="co2_constant" required class="w-full px-4 py-2.5 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-stone-800">
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-stone-500 hover:bg-stone-100 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition shadow-sm shadow-emerald-200">Update Value</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection