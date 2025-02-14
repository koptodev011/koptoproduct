<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessCategory extends Model
{
    public function tenant()
    {
        return $this->hasMany(Tenant::class);
    }

    public function tenantUnit()
    {
        return $this->hasMany(TenantUnit::class);
    }

    
}
