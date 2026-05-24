<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
     protected $table = 'stocks';
    protected $fillable = [
        'item_id',
        'item_barcodes_id',
        'barcode',
        'quantity',
        'uom',
        'cost_price',
        'created_by',
        'updated_by',
        'soft_delete',
        'duplicate_flag',
        'isPublic',
        'stock_out_display',
    ];


    public function item(){
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
