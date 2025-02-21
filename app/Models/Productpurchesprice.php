<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productpurchesprice extends Model
{
    protected $fillable = [

        'product_purches_price',
        'withorwithouttax'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
