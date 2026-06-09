<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model {
    protected $fillable = [
        'title', 'hashtag', 'description', 'start_date', 'end_date', 'is_active'
    ];
}