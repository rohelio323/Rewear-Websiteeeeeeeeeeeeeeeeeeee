<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = [
        'title', 
        'description', 
        'start_date', 
        'end_date', 
        'is_active'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'challanges_id');
    }
}