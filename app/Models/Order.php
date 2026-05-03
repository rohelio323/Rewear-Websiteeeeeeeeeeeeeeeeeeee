<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'buyer_id',
        'item_id',
        'status',
        'total_price',
        'co2_saved_amount',
        'payment_reference',
        'users_id',
        'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'co2_saved_amount' => 'decimal:2',
        ];
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
