<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partyaddationalfields extends Model
{
    protected $fillable = [
        'addational_field_name',
        'addational_field_data',
        'party_id'
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
