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
        'seller_request_status',
        'seller_request_reason',
        'seller_rejection_note',
        'seller_requested_at',
        'seller_reviewed_at',
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
            'seller_requested_at' => 'datetime',
            'seller_reviewed_at' => 'datetime',
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

    public function isVerifiedSeller(): bool
    {
        return (bool) $this->is_verified_seller;
    }

    public function hasPendingSellerRequest(): bool
    {
        return $this->seller_request_status === 'pending';
    }

    public function wasRejectedAsSeller(): bool
    {
        return $this->seller_request_status === 'rejected';
    }

    public function canApplyAsSeller(): bool
    {
        return in_array($this->seller_request_status, ['none', 'rejected']);
    }

    public function submitSellerRequest(string $reason): void
    {
        $this->update([
            'seller_request_status' => 'pending',
            'seller_request_reason' => $reason,
            'seller_requested_at' => now(),
            'seller_rejection_note' => null,
        ]);
    }

    public function approveAsSeller(): void
    {
        $this->update([
            'is_verified_seller' => true,
            'seller_request_status' => 'approved',
            'seller_reviewed_at' => now(),
        ]);
    }

    public function rejectAsSeller(string $note): void
    {
        $this->update([
            'seller_request_status' => 'rejected',
            'seller_rejection_note' => $note,
            'seller_reviewed_at' => now(),
        ]);
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

    // Seller Reputation
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    public function averageRating(): float
    {
        return round($this->reviewsReceived()->avg('rating') ?? 0, 1);
    }

    public function totalReviews(): int
    {
        return $this->reviewsReceived()->count();
    }
    
    public function reviewsReceivedList()
    {
    return $this->hasMany(Review::class, 'seller_id')
        ->with(['item', 'buyer'])
        ->latest();
    }
    
}

