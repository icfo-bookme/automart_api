<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
     public function index()
    {
        $sections = Section::where('soft_delete', 0)
            ->whereHas('items', function ($query) {
                $query->where('soft_delete', 0); 
            })
            ->orderBy('section_order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sections
        ]);
    }
}
