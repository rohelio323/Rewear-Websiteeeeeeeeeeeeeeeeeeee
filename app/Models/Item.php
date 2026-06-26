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
        'photo_path',
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

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'item_user')
                    ->withTimestamps();
    }

    public function getFirstPhotoAttribute(): ?string
    {
        $photos = $this->photo_path;
        return $photos && count($photos) > 0 ? $photos[0] : null;
    }

    public function getPhotoUrlAttribute(): string
    {
        $photo = $this->first_photo;
        if (!$photo) {
            return 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=500&h=667&fit=crop&q=80';
        }
        return str_starts_with($photo, 'http') ? $photo : asset('storage/' . $photo);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
