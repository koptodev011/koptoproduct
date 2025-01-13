<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productstock extends Model
{
    public function stock()
{
    return $this->hasOne(Productstock::class);
}

}
