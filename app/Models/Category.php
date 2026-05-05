<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['category_name', 'co2_constant'];

    protected function casts(): array {
        return ['co2_constant' => 'decimal:2'];
    }

    public function items() {
        return $this->hasMany(Item::class);
    }
}
