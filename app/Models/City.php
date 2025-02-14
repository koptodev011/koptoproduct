<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
 
    public function tenant()
    {
        return $this->hasOne(Tenant::class, 'city_id'); // Specify the foreign key
    }


    public function tenantUnit()
    {
        return $this->hasMany(TenantUnit::class);
    }
}


