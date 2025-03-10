<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'model',
        'make',
        'year',
        'is_available'
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
