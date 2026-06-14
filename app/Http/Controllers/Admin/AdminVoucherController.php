<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarbonVoucher;
use Illuminate\Http\Request;

class AdminVoucherController extends Controller
{
    public function index()
    {
        $vouchers = CarbonVoucher::withCount('redemptions')->latest()->paginate(15);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'               => 'required|string|max:50|unique:carbon_vouchers,code',
            'discount_amount'    => 'required|numeric|min:0',
            'co2_cost'           => 'required|integer|min:1',
            'quantity_available' => 'required|integer|min:1',
            'is_active'          => 'boolean',
        ]);

        CarbonVoucher::create(array_merge($validated, [
            'is_active' => $request->boolean('is_active', true),
        ]));

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher created successfully!');
    }

    public function update(Request $request, CarbonVoucher $voucher)
    {
        $validated = $request->validate([
            'discount_amount'    => 'required|numeric|min:0',
            'co2_cost'           => 'required|integer|min:1',
            'quantity_available' => 'required|integer|min:0',
            'is_active'          => 'boolean',
        ]);

        $voucher->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher updated!');
    }

    public function destroy(CarbonVoucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher deleted.');
    }
}
