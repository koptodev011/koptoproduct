<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    public function tenant()
    {
        return $this->hasMany(Tenant::class);
    }

    public function tenantUnit()
    {
        return $this->hasMany(TenantUnit::class);
    }


    // public function tenant()
    // {
    //     return $this->belongsTo(Tenant::class);
    // }
}
