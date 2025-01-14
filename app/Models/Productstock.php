<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productstock extends Model
{
    protected $fillable = [
        'product_id',
        'product_stock',
        'at_price',
        'min_stock',
        'location'
    ];

    public function stock()
    {
        return $this->hasOne(Productstock::class);
    }

}
