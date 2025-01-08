<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    public function shippingAddresses()
    {
        return $this->hasMany(Shippingaddress::class);
    }

    public function additionalFields()
    {
        return $this->hasMany(Partyaddationalfields::class);
    }
}
