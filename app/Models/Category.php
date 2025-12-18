<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';

    protected $fillable = [
        'name',
        'priority',
        'created_by',
        'updated_by',
        'soft_delete'
    ];

    public $timestamps = true;

    public function scopeActive($query)
    {
        return $query->where('soft_delete', 0);
    }

    // Category.php
    public function sub_categories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
}
