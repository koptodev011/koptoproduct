<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'product_hsn',
        'product_base_unit',
        'description',
        'mrp',
        'category_id',
        'item_code',
        'tax_id'
    ];
    
    public function unitConversion()
    {
        return $this->hasOne(Productunitconversion::class, 'product_id');
    }

    public function pricing()
    {
        return $this->hasOne(Productpricing::class, 'product_id');
    }

    public function wholesalePrice()
    {
        return $this->hasOne(Productwholesaleprice::class, 'product_id');
    }

    public function stock()
    {
        return $this->hasOne(Productstock::class, 'product_id');
    }

    public function onlineStore()
    {
        return $this->hasOne(Productonlinestore::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
}
