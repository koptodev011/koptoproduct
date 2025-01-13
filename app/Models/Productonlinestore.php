<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productonlinestore extends Model
{
    public function onlineStore()
{
    return $this->hasOne(Productonlinestore::class);
}

}
