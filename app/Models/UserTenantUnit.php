<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class UserTenantUnit extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'tenant_id', 'tenant_type'];

    public function tenantable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'tenant_type', 'tenant_id');
    }
}
