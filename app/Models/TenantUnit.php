<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TenantUnit extends Model
{
     
    protected $fillable = [
        'business_name',
        'business_type_id',
        'business_address',
        'phone_number',
        'business_category_id',
        'TIN_number',
        'state_id',
        'business_email',
        'pin_code',
        'business_logo',
        'business_signature',
        'user_id',
        'isactive',
        'city_id',
        'isonlinestore'
    ];

  


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businesstype()
    {
        return $this->belongsTo(BusinessType::class, 'business_type_id');
    }

    // public function businesstype(){
    //     return $this->hasOne(BusinessType::class);
    // }
    public function businesscategory()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id'); // Specify the foreign key if it’s not 'city_id'
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
    
 

}
