<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarbonVoucher extends Model
{
    protected $fillable = ['code', 'discount_amount', 'co2_cost', 'quantity_available', 'is_active'];

    protected $casts = [
        'is_active'       => 'boolean',
        'discount_amount' => 'decimal:2',
    ];

    public function redemptions()
    {
        return $this->hasMany(VoucherRedemption::class, 'voucher_id');
    }
}
