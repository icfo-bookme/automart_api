<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    // Table name (optional if table name follows Laravel convention)
    protected $table = 'section';

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'section_order',
        'created_by',
        'updated_by',
        'soft_delete',
    ];

  public function items()
    {
        return $this->hasMany(Item::class)->where('soft_delete', 0);
    }

}