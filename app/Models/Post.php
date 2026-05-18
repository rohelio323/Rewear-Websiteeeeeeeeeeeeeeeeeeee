<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    protected $primaryKey = 'post_id';

    protected $fillable = [
        'title',
        'content',
        'users_id',
        'upvote_count',
        'image_path',
        'challanges_id'
    ];
    
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class, 'challanges_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

}
