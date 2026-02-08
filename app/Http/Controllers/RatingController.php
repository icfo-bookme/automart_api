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
}
