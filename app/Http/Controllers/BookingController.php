<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function store(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'sale_id' => 'nullable|integer',
            'firstName' => 'required|string|max:100',
            'lastName' => 'nullable|string|max:100',
            'phoneNumber' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',

            'country' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'thana' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'road_no' => 'nullable|string|max:50',
            'house_no' => 'nullable|string|max:50',
            'flat_no' => 'nullable|string|max:50',
            'car_no' => 'nullable|string|max:50',

            'booking_notes' => 'nullable|string',
            'customer_notes' => 'nullable|string',
            'remarks' => 'nullable|string',

            'advance_payment' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',
            'shipping_amount' => 'nullable|numeric',


            'invoice_date' => 'nullable|date',

            // booking details
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.barcode_id' => 'nullable|integer',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // ✅ Create Booking
            $booking = Booking::create([
                'sale_id' => $request->sale_id,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone_number' => $request->phoneNumber,
                'email' => $request->email,
                'country' => $request->country,
                'district' => $request->district,
                'city' => $request->city,
                'thana' => $request->thana,
                'area' => $request->area,
                'road_no' => $request->road_no,
                'house_no' => $request->house_no,
                'flat_no' => $request->flat_no,
                'car_no' => $request->car_no,
                'booking_notes' => $request->booking_notes,
                'customer_notes' => $request->customer_notes,
                'remarks' => $request->remarks,
                'advance_payment' => $request->advance_payment ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'shipping_amount' => $request->shipping_amount ?? 0,
                'status' => 0,
                'invoice_date' => $request->invoice_date ?? null,
                'soft_delete' => 0,
                'created_by' => auth()->id(),
            ]);

            // ✅ Create Booking Details
            foreach ($request->items as $item) {
                $booking->bookingDetails()->create([
                    'booking_id' => $booking->id,
                    'product_id' => $item['product_id'],
                    'barcode_id' =>  null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['unit_price'] * $item['quantity'],
                    'cost_price' => $item['cost_price'] ?? 0,
                    'soft_delete' => 0,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Booking created successfully',
                'data' => $booking->load('bookingDetails')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
