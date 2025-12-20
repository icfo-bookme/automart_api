<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;

    protected $table = 'item';

    protected $primaryKey = 'id';

    // Mass assignable fields
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'brand_id',
        'section_id',
        'name',
        'barcode',
        'length',
        'height',
        'width',
        'regular_price',
        'minimum_order_quantity',
        'sales_price',
        'cost_price',
        'minimum_price',
        'thumbnail',
        'resized_image',
        'details',
        'specification_details',
        'sales_type',
        'is_published',
        'car_company_id',
        'car_brand_id',
        'car_model_id',
        'is_outsourced',
        'created_by',
        'updated_by',
        'has_watermark',
    ];

    // Dates (for soft delete and timestamps)
    protected $dates = [
        'created_at',
        'updated_at',

    ];

    // Casts (optional, type casting for fields)
    protected $casts = [
        'is_published' => 'boolean',
        'is_outsourced' => 'boolean',
        'has_watermark' => 'boolean',
        'length' => 'float',
        'height' => 'float',
        'width' => 'float',
        'regular_price' => 'decimal:2',
        'sales_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'minimum_price' => 'decimal:2',
        'minimum_order_quantity' => 'integer',
    ];

    // Relationships (optional, if needed)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Local Scope for active items
    public function scopeActive($query)
    {
        return $query->where('is_published', 1)
            ->where('soft_delete', 0);
    }


    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    // public function brand()
    // {
    //     return $this->belongsTo(Brand::class, 'brand_id');
    // }

    // public function section()
    // {
    //     return $this->belongsTo(Section::class, 'section_id');
    // }
}
