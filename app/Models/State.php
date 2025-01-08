<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
}
