<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'product_amount',
        'unique_key',
        'tenant_id',
        
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
