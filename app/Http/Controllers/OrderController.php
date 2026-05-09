<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PurchaseItemBarcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function myOrders(Request $request)
    {

        $orders = Order::where('email', $request->user()->email)
            ->where('soft_delete', 0)
            ->with('order_details')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            // Customer Info
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',

            // Address
            'country' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'thana' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'road_no' => 'nullable|string|max:50',
            'flat_no' => 'nullable|string|max:50',
            'car_no' => 'nullable|string|max:50',

            // Order Info
            'order_notes' => 'nullable|string',
            'customer_notes' => 'nullable|string',
            'remarks' => 'nullable|string',

            'advance_payment' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',

            // Items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.barcode' => 'nullable|integer',
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

            // ✅ Create Order
            $order = Order::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'order_code' => 'ORD-' . strtoupper(uniqid()),
                'country' => $request->country,
                'district' => $request->district,
                'city' => $request->city,
                'thana' => $request->thana,
                'area' => $request->area,
                'road_no' => $request->road_no,
                'flat_no' => $request->flat_no,
                'car_no' => $request->car_no,

                'order_notes' => $request->order_notes,
                'customer_notes' => $request->customer_notes,
                'remarks' => $request->remarks,

                'advance_payment' => $request->advance_payment ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,

                'status' => 0,
                'soft_delete' => 0,
                'created_by' => auth()->id(),
            ]);

            // ✅ Insert Order Details
            foreach ($request->items as $item) {
                if ($item['barcode']) {
                    $barcode_id = PurchaseItemBarcode::where('item_id', $item['product_id'])
                        ->where('soft_delete', 0)
                        ->first()->id;
                }

                $order->order_details()->create([
                    'product_id' => $item['product_id'],
                    'barcode_id' => $barcode_id ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'price' => $item['unit_price'] * $item['quantity'],
                    'cost_price' => $item['cost_price'] ?? 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => $order->load('order_details')
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
