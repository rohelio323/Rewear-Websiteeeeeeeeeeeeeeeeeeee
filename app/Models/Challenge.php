<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model {
    protected $fillable = [
        'title', 'hashtag', 'description', 'reward_points', 'start_date', 'end_date', 'is_active'
    ];
}