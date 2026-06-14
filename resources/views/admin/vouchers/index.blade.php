@extends('layouts.admin')
@section('title', 'Carbon Vouchers')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-end justify-between gap-6 mb-8">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-1">Admin</p>
            <h1 class="text-3xl font-extrabold text-emerald-950 tracking-tight">Carbon Vouchers</h1>
        </div>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-2.5 bg-emerald-950 hover:bg-emerald-800 text-white rounded-xl text-sm font-bold transition shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> New Voucher
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-2xl text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-3 rounded-2xl text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    
    <div class="bg-white border border-stone-200 rounded-3xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 border-b border-stone-200">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-widest text-stone-500">Code</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-widest text-stone-500">Discount</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-widest text-stone-500">CO₂ Cost</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-widest text-stone-500">Qty Left</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-widest text-stone-500">Redeemed</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-widest text-stone-500">Active</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-widest text-stone-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($vouchers as $voucher)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="px-6 py-4 font-mono font-bold text-emerald-800">{{ $voucher->code }}</td>
                        <td class="px-6 py-4 font-semibold text-stone-900">Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-stone-600">{{ $voucher->co2_cost }} kg</td>
                        <td class="px-6 py-4 text-stone-600">{{ $voucher->quantity_available }}</td>
                        <td class="px-6 py-4 text-stone-600">{{ $voucher->redemptions_count }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest
                                {{ $voucher->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-500' }}">
                                {{ $voucher->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST"
                                  onsubmit="return confirm('Delete this voucher?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-16 text-center text-stone-400 text-sm">No vouchers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-stone-100">{{ $vouchers->links() }}</div>
    </div>
</div>

{{-- Create Modal --}}
<div id="createModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl relative">
        <button onclick="document.getElementById('createModal').classList.add('hidden')"
            class="absolute top-5 right-6 text-stone-400 hover:text-red-500 text-xl font-bold">✕</button>
        <h2 class="text-xl font-bold text-emerald-950 mb-6">Create Voucher</h2>
        <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-stone-600 uppercase tracking-widest mb-2">Code *</label>
                <input type="text" name="code" placeholder="e.g. ECO50" required
                    class="w-full border border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-widest mb-2">Discount (Rp) *</label>
                    <input type="number" name="discount_amount" min="0" step="1000" required
                        class="w-full border border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-600 uppercase tracking-widest mb-2">CO₂ Cost (kg) *</label>
                    <input type="number" name="co2_cost" min="1" required
                        class="w-full border border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-600 uppercase tracking-widest mb-2">Quantity Available *</label>
                <input type="number" name="quantity_available" min="1" required
                    class="w-full border border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-800">
            </div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded accent-emerald-800">
                <span class="text-sm font-medium text-stone-700">Active immediately</span>
            </label>
            <button type="submit"
                class="w-full py-3 bg-emerald-900 text-white font-bold rounded-full text-sm hover:bg-emerald-800 transition">
                Create Voucher
            </button>
        </form>
    </div>
</div>
@endsection
