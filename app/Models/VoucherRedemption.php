<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherRedemption extends Model
{
    protected $fillable = ['user_id', 'voucher_id', 'co2_deducted', 'order_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(CarbonVoucher::class, 'voucher_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
