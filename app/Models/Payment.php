<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'rental_id',
        'amount',
        'statut'
    ];

    public function rentals()
    {
        return $this->belongsTo(Rental::class);
    }
}
