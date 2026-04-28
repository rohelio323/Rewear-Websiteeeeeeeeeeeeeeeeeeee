<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $fillable = [
        'item_name',
        'description',
        'size',
        'condition',
        'price',
        'photo_path', //nanti di store pake array biar bisa banyak 
        'status',
        'users_id',
        'category_id',
    ];

    protected function casts(): array {
        return [
            'photo_path' => 'array',
            'price' => 'decimal:2',
        ];
    }

    public function user() {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function category()  {
        return $this->belongsTo(Category::class);
    }
// ===== WISHLIST RELATIONSHIP =====
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'item_user')
                    ->withTimestamps();
    }


}
