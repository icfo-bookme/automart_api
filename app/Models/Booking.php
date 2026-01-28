<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'sale_id',
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
        'house_no',
        'flat_no',
        'car_no',
        'booking_notes',
        'customer_notes',
        'remarks',
        'advance_payment',
        'discount_amount',
        'shipping_amount',
        'status',
        'invoice_date',
        'soft_delete',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'advance_payment' => 'float',
        'discount_amount' => 'float',
        'shipping_amount' => 'float',
    ];

    /**
     * One booking has many booking details
     */
    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'booking_id');
    }
}
