@extends('layouts.admin')
@section('title', 'Challenge Management')

@section('content')
{{-- Alpine wrapper for modal states and flash message auto-dismiss --}}
<div x-data="{ 
        showAddModal: false, 
        showSuccess: true
    }" class="font-body max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-1 font-label">Administration</p>
            <h1 class="text-3xl font-extrabold text-emerald-950 tracking-tight font-headline">Challenge Management</h1>
            <p class="text-sm text-stone-500 mt-1">Create and monitor community events to drive user engagement.</p>
        </div>
        
        <div class="flex-shrink-0">
            <button @click="showAddModal = true" class="flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-950 hover:bg-emerald-800 text-white rounded-xl text-sm font-bold transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 active:scale-95 active:translate-y-0">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Add Challenge
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

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50/80">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">Challenge Details</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label">Timeline</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-stone-500 font-label text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($challenges as $challenge)
                    <tr class="hover:bg-stone-50 transition-colors group/row">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-stone-100 flex items-center justify-center text-stone-500 group-hover/row:bg-emerald-50 group-hover/row:text-emerald-600 transition-colors shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">campaign</span>
                                </div>
                                <div>
                                    <span class="block font-bold text-stone-900 font-headline text-base">{{ $challenge->title }}</span>
                                    <span class="block text-xs text-stone-500 mt-0.5 truncate max-w-xs">{{ $challenge->description }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-stone-50 text-stone-700 border border-stone-200 font-mono text-xs font-bold shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-stone-400">calendar_month</span>
                                {{ \Carbon\Carbon::parse($challenge->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($challenge->end_date)->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($challenge->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider font-label">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-stone-100 text-stone-600 text-[10px] font-bold uppercase tracking-wider font-label">
                                    <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span> Closed
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-24 text-center text-stone-400 font-body">
                            <div class="w-20 h-20 mx-auto bg-stone-50 rounded-full flex items-center justify-center mb-4 border border-stone-100 shadow-inner">
                                <span class="material-symbols-outlined text-4xl text-stone-300">emoji_events</span>
                            </div>
                            <p class="font-bold text-stone-600 text-base mb-1">No challenges created yet</p>
                            <p class="text-sm text-stone-400 max-w-sm mx-auto mb-4">Start engaging your users by creating the first community challenge.</p>
                            <button @click="showAddModal = true" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors">
                                + Create your first challenge
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= Add Challenge Modal ================= --}}
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
                            <h2 class="font-extrabold text-stone-900 text-xl font-headline leading-tight">Create Challenge</h2>
                            <p class="text-xs text-stone-500 mt-0.5">Define a new community event.</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.challenges.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">Challenge Title</label>
                            <input type="text" name="title" required placeholder="e.g., Upcycled Denim Week" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 placeholder-stone-400 bg-stone-50 focus:bg-white">
                        </div>

                        <div class="mb-4">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">Description</label>
                            <textarea name="description" required rows="3" placeholder="What are the rules and rewards?" class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 placeholder-stone-400 bg-stone-50 focus:bg-white resize-none"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">Start Date</label>
                                <input type="date" name="start_date" required class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 bg-stone-50 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-stone-500 mb-2 font-label">End Date</label>
                                <input type="date" name="end_date" required class="w-full px-4 py-3 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors text-stone-800 bg-stone-50 focus:bg-white">
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-stone-600 hover:bg-stone-100 transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-950 hover:bg-emerald-800 text-white text-sm font-bold transition shadow-md active:scale-95">Launch Challenge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection