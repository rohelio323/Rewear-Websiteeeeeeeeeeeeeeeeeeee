@extends('layouts.admin')
@section('title', 'CO2 Categories')

@section('content')
{{-- We wrap everything in an Alpine component to manage the modal states --}}
<div x-data="{ 
        showAddModal: false, 
        showEditModal: false, 
        editId: '', 
        editName: '', 
        editCo2: '' 
    }">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:2rem;gap:1rem;flex-wrap:wrap;">
        <div>
            <h1 style="font-family:'Manrope',sans-serif;font-size:2rem;font-weight:800;color:#1A2820;letter-spacing:-0.03em;margin-bottom:0.25rem;">CO2 Categories</h1>
            <p style="font-size:0.9375rem;color:#5A6B60;line-height:1.5;max-width:480px;">Manage clothing categories and their associated environmental impact values.</p>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;">
            {{-- Add Button triggers the Add Modal --}}
            <button @click="showAddModal = true" style="display:flex;align-items:center;gap:7px;background:#1A2820;border:none;color:#fff;padding:0.5rem 1.125rem;border-radius:10px;font-size:0.875rem;font-weight:600;cursor:pointer;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Category
            </button>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div style="background:#FEE2E2;border:1px solid #F87171;color:#B91C1C;padding:1rem;border-radius:8px;margin-bottom:1.5rem;">
            <ul style="margin:0;padding-left:1.5rem;font-size:0.875rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Data Table --}}
    <div style="background:#fff;border:1.5px solid #E2E2DE;border-radius:14px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;text-align:left;">
            <thead style="background:#F0F5F2;border-bottom:1.5px solid #E2E2DE;">
                <tr>
                    <th style="padding:1rem 1.5rem;font-size:0.8125rem;font-weight:700;color:#5A6B60;text-transform:uppercase;letter-spacing:0.05em;">Category Name</th>
                    <th style="padding:1rem 1.5rem;font-size:0.8125rem;font-weight:700;color:#5A6B60;text-transform:uppercase;letter-spacing:0.05em;">CO2 Saved (per item)</th>
                    <th style="padding:1rem 1.5rem;font-size:0.8125rem;font-weight:700;color:#5A6B60;text-transform:uppercase;letter-spacing:0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr style="border-bottom:1px solid #E2E2DE;">
                    <td style="padding:1rem 1.5rem;font-size:0.875rem;font-weight:600;color:#1A2820;">
                        {{ $category->category_name }}
                    </td>
                    <td style="padding:1rem 1.5rem;">
                        <span style="background:#EBF8F2;color:#059669;padding:4px 10px;border-radius:6px;font-size:0.8125rem;font-weight:700;">
                            {{ $category->co2_constant ?? 0 }} kg
                        </span>
                    </td>
                    <td style="padding:1rem 1.5rem;display:flex;align-items:center;gap:12px;">
                        {{-- Edit Button triggers Edit Modal and passes data to it --}}
                        <button type="button" 
                                @click="showEditModal = true; editId = '{{ $category->id }}'; editName = '{{ $category->category_name }}'; editCo2 = '{{ $category->co2_constant }}'" 
                                style="background:none;border:none;color:#5A6B60;font-weight:600;font-size:0.875rem;cursor:pointer;">
                            Edit
                        </button>

                        {{-- Delete Form --}}
                        <form action="{{ url('/admin/categories/' . $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#DC2626;font-weight:600;font-size:0.875rem;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding:2rem 1.5rem;text-align:center;color:#8A9E94;font-size:0.875rem;">
                        No CO2 categories found in the database.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= Add Category Modal ================= --}}
    <div x-show="showAddModal" style="display:none;" x-transition.opacity>
        {{-- Modal Backdrop --}}
        <div style="position:fixed;inset:0;background:rgba(26,40,32,0.4);backdrop-filter:blur(2px);z-index:50;display:flex;align-items:center;justify-content:center;">
            {{-- Modal Box --}}
            <div @click.away="showAddModal = false" style="background:#fff;width:100%;max-width:400px;border-radius:14px;padding:1.5rem;box-shadow:0 10px 25px rgba(0,0,0,0.1);">
                <h2 style="font-family:'Manrope',sans-serif;font-size:1.25rem;font-weight:800;color:#1A2820;margin-bottom:1rem;">Add New Category</h2>
                
                <form action="{{ url('/admin/categories') }}" method="POST">
                    @csrf
                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:0.8125rem;font-weight:700;color:#5A6B60;margin-bottom:0.5rem;">Category Name</label>
                        <input type="text" name="category_name" required placeholder="e.g., Winter Coats" style="width:100%;padding:0.75rem;border:1.5px solid #E2E2DE;border-radius:8px;font-size:0.875rem;box-sizing:border-box;">
                    </div>
                    
                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block;font-size:0.8125rem;font-weight:700;color:#5A6B60;margin-bottom:0.5rem;">CO2 Saved (kg)</label>
                        <input type="number" step="0.01" name="co2_constant" required placeholder="e.g., 15.50" style="width:100%;padding:0.75rem;border:1.5px solid #E2E2DE;border-radius:8px;font-size:0.875rem;box-sizing:border-box;">
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" @click="showAddModal = false" style="padding:0.625rem 1rem;background:#F0F5F2;border:none;border-radius:8px;font-size:0.875rem;font-weight:600;color:#5A6B60;cursor:pointer;">Cancel</button>
                        <button type="submit" style="padding:0.625rem 1rem;background:#1A2820;border:none;border-radius:8px;font-size:0.875rem;font-weight:600;color:#fff;cursor:pointer;">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= Edit Category Modal ================= --}}
    <div x-show="showEditModal" style="display:none;" x-transition.opacity>
        {{-- Modal Backdrop --}}
        <div style="position:fixed;inset:0;background:rgba(26,40,32,0.4);backdrop-filter:blur(2px);z-index:50;display:flex;align-items:center;justify-content:center;">
            {{-- Modal Box --}}
            <div @click.away="showEditModal = false" style="background:#fff;width:100%;max-width:400px;border-radius:14px;padding:1.5rem;box-shadow:0 10px 25px rgba(0,0,0,0.1);">
                <h2 style="font-family:'Manrope',sans-serif;font-size:1.25rem;font-weight:800;color:#1A2820;margin-bottom:1rem;">Edit CO2 Value</h2>
                
                {{-- Alpine binds the form action dynamically to the correct ID --}}
                <form :action="'{{ url('/admin/categories') }}/' + editId + '/co2-constant'" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:0.8125rem;font-weight:700;color:#5A6B60;margin-bottom:0.5rem;">Category Name</label>
                        {{-- Assuming you only update the CO2 constant based on your routing, we make this readonly --}}
                        <input type="text" x-model="editName" name="category_name" readonly style="width:100%;padding:0.75rem;background:#F7F8F6;border:1.5px solid #E2E2DE;border-radius:8px;font-size:0.875rem;box-sizing:border-box;color:#8A9E94;">
                    </div>
                    
                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block;font-size:0.8125rem;font-weight:700;color:#5A6B60;margin-bottom:0.5rem;">CO2 Saved (kg)</label>
                        <input type="number" step="0.01" x-model="editCo2" name="co2_constant" required style="width:100%;padding:0.75rem;border:1.5px solid #E2E2DE;border-radius:8px;font-size:0.875rem;box-sizing:border-box;">
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" @click="showEditModal = false" style="padding:0.625rem 1rem;background:#F0F5F2;border:none;border-radius:8px;font-size:0.875rem;font-weight:600;color:#5A6B60;cursor:pointer;">Cancel</button>
                        <button type="submit" style="padding:0.625rem 1rem;background:#1A2820;border:none;border-radius:8px;font-size:0.875rem;font-weight:600;color:#fff;cursor:pointer;">Update Value</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection