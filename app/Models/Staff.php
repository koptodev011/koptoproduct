<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'mobile_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
