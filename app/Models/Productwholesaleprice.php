<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productwholesaleprice extends Model
{
    public function wholesalePrice()
{
    return $this->hasOne(Productwholesaleprice::class);
}

}
