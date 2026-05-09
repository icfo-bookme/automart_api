<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'country',
        'district',
        'city',
        'thana',
        'area',
        'road_no',
        'flat_no',
        'car_no',
        'order_code',
        'order_notes',
        'customer_notes',
        'delivery_type',
        'is_approve',
        'is_rejected',
        'rejected_by',
        'is_shipment',
        'is_payment',
        'status',
        'is_shipment_charge_applied',
        'discount_amount',
        'advance_payment',
        'collected_payment',
        'payment_due',
        'sales_by',
        'created_by',
        'updated_by',
        'soft_delete',
        'remarks'
    ];

  
    /**
     * One booking has many booking details
     */
   public function order_details(){
        return $this->hasMany(OrderDetails::class,'order_id','id');
    }
}
