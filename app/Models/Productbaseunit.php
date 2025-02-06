<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productbaseunit extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_base_unit',
        'is_delete',
        'shortname'
    ];
}
