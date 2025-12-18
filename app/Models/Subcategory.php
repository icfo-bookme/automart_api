<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'sub_category';

    // Mass assignable fields
    protected $fillable = [
        'category_id',
        'name',
        'created_by',
        'updated_by',
        'soft_delete'
    ];

    // Timestamps
    public $timestamps = true;

    // Scope to get only active subcategories
    public function scopeActive($query)
    {
        return $query->where('soft_delete', 0);
    }

    // Relationship: Belongs to Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
