<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paymentin extends Model
{
    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    // Define the relationship with the TenantUnit model
    public function tenantUnit()
    {
        return $this->belongsTo(TenantUnit::class);
    }
}
