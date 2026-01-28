<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    use HasFactory;

    protected $table = 'booking_details';

    protected $fillable = [
        'booking_id',
        'product_id',
        'barcode_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
        'cost_price',
        'soft_delete',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'total_price' => 'float',
        'cost_price' => 'float',
    ];

    /**
     * Booking detail belongs to a booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
