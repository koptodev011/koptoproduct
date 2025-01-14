<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productpricing extends Model
{
    protected $fillable = [
        'product_id',
        'sale_price',
        'withorwithouttax',
        'discount',
        'percentageoramount'
    ];

    public function pricing()
    {
        return $this->hasOne(Productpricing::class);
    }
}
