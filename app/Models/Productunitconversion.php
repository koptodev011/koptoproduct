<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productunitconversion extends Model
{
    protected $fillable = [
        'product_id',
        'product_base_unit_id',
        'product_secondary_unit_id',
        'conversion_rate'
    ];

    public function unitConversion()
    {
        return $this->hasOne(Productunitconversion::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productconversion_id', 'id');
    }
}
