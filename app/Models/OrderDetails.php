<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    protected $fillable = [
        'order_id',
        'product_id',
        'barcode_id',
        'product_name',
        'quantity',
        'unit_price',
        'price',
        'cost_price'
    ];

    /**
     * Booking detail belongs to a booking
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
     public function purchase_item_barcodes(){
        return $this->belongsTo(PurchaseItemBarcode::class, 'barcode_id', 'id');
    }
}
