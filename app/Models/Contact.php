<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'contact_table';

    // Primary key
    protected $primaryKey = 'id';

    // Auto increment bigint
    public $incrementing = true;
    protected $keyType = 'int';

    // Timestamps enabled
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'message',
        'type',
        'is_replied',
        'soft_delete',
    ];

    // Casts based on DB types
    protected $casts = [
        'is_replied'  => 'boolean',
        'soft_delete' => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
}
