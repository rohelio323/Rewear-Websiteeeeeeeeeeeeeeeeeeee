<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostVote extends Model
{
    protected $fillable = ['post_id', 'user_id', 'value'];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
