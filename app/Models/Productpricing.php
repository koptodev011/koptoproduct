<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productpricing extends Model
{
    public function pricing()
{
    return $this->hasOne(Productpricing::class);
}

}
