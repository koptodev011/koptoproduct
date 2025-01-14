<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;

class Productstockadjectment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stock_quantity',
        'priceperunit',
        'addorreduct_product_stock',
        'details'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
