<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItemBarcode extends Model
{
    protected $fillable = [
        'purchase_id',
        'purchase_detail_id',
        'item_id',
        'barcode',
        'soft_delete',
        'regular_price',
        'sales_price',
        'barcode_image'
    ];

    public function order_details()
    {
        return $this->hasMany(OrderDetails::class, 'barcode_id', 'id');
    }
}
