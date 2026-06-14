<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reportable_type',
        'reportable_id',
        'reporter_id',
        'reason',
        'status',
    ];


    public function reportable()
    {
        return $this->morphTo();
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
