<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productwholesaleprice extends Model
{
    protected $fillable = [
        'product_id',
        'whole_sale_price',
        'withorwithouttax',
        'wholesale_min_quantity'
    ];

    public function wholesalePrice()
    {
        return $this->hasOne(Productwholesaleprice::class);
    }
}
