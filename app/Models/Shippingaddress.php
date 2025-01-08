<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shippingaddress extends Model
{
    protected $fillable = [
        'shipping_address',
        'party_id'
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
