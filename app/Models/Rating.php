<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    // Table name (optional if it follows Laravel naming convention)
    protected $table = 'ratings';

    // Fillable fields for mass assignment
    protected $fillable = [
        'item_id',
        'rating',
        'review',
        'name',
        'email',
        'soft_delete',
    ];

    // If you want to treat soft_delete manually instead of Laravel's soft deletes
    protected $casts = [
        'soft_delete' => 'boolean',
    ];

    // If you are using default timestamps (created_at, updated_at)
    public $timestamps = true;
   
}
