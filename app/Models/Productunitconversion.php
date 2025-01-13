<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productunitconversion extends Model
{
    public function unitConversion()
{
    return $this->hasOne(Productunitconversion::class);
}

}
