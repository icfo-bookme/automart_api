<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index($itemId)
    {
        $ratings = Rating::where('item_id', $itemId)->get();
        return response()->json($ratings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $rating = Rating::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => $rating
        ], 201);
    }
}
