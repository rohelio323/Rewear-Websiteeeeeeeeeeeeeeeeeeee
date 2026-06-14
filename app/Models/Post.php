<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\PostVote;

class Post extends Model {
    protected $primaryKey = 'post_id';

    protected $fillable = [
        'status ',
        'title',
        'content',
        'users_id',
        'upvote_count',
        'tags',
        'image_path',
        'challanges_id'
    ];
    
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class, 'challanges_id');
    }

    public function votes()
    {
        return $this->hasMany(PostVote::class, 'post_id', 'post_id');
    }

    public function recalculateScore(): void
    {
        $this->update([
            'upvote_count' => $this->votes()->sum('value'),
        ]);
    }

}
