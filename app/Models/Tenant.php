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

    public function businesstype()
    {
        return $this->belongsTo(BusinessType::class, 'business_types_id');
    }

    // public function businesstype(){
    //     return $this->hasOne(BusinessType::class);
    // }
    public function businesscategory()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_categories_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
