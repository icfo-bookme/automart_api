<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductRequest extends Model
{
    use HasFactory, SoftDeletes;

    // Table name
    protected $table = 'product_requests';

    // Primary key
    protected $primaryKey = 'id';

    // Auto-incrementing ID
    public $incrementing = true;

    // Data type of primary key
    protected $keyType = 'int';

    // Timestamps (created_at, updated_at)
    public $timestamps = true;

    // Soft delete column (your table has `soft_delete`)
    const DELETED_AT = 'soft_delete';

    // Mass assignable attributes
    protected $fillable = [
        'user_name',
        'user_phone',
        'user_email',
        'product_detail',
        'product_image',
        'is_approved',
    ];

    // Casts
    protected $casts = [
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'soft_delete' => 'datetime',
    ];
}
