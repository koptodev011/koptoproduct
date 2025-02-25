<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_type', 'party_id', 'billing_name', 'phone_number',
        'po_number', 'po_date', 'tax_amount', 'received_amount',
        'payment_type', 'sale_description', 'sale_image',
        'user_id', 'status'
    ];

    public function productSales()
    {
        return $this->hasMany(ProductSale::class, 'sale_id');
    }
}
