<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
}
