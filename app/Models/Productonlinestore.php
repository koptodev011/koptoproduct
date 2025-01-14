<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productonlinestore extends Model
{
    protected $fillable = [
        'product_id',
        'online_store_price',
        'online_product_description'
    ];

    public function onlineStore()
    {
        return $this->hasOne(Productonlinestore::class);
    }

}
