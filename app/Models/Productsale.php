<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productsale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'quantity', 'amount', 'unit_id',
        'priceperunit', 'discount_percentage', 'discount_amount',
        'tax_percentage', 'tax_amount', 'sale_id'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
}
