<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_verified_seller',
        'total_co2_saved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified_seller' => 'boolean',
            'total_co2_saved' => 'decimal:2',
        ];
    }

    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'item_user')
                    ->withTimestamps();
    }


    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Relationships
    public function items()
    {
        return $this->hasMany(Item::class, 'users_id');
    }

    public function buyerOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sellerOrders()
    {
        return $this->hasMany(Order::class, 'users_id');
    }


}

