<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    
    protected $fillable = [
        'business_name',
        'business_type',
        'business_address',
        'phone_number',
        'business_category',
        'TIN_number',
        'state',
        'business_email',
        'pin_code',
        'business_logo',
        'business_signature',
        'user_id',
        'isactive'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
