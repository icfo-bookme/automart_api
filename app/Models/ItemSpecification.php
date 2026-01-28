<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSpecification extends Model
{
    use HasFactory;

    protected $table = 'item_specification';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'item_id',
        'name',
        'details',
        'created_by',
        'updated_by',
        'soft_delete',
    ];

    protected $casts = [
        'soft_delete' => 'boolean',
    ];
}
