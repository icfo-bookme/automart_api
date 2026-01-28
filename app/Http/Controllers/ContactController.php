<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Store contact form data
     */
    public function store(Request $request): JsonResponse
    {
     

        $validated = $request->validate([
            'name'           => 'required|string|max:50',
            'email'          => 'required|email|max:191',
            'number' => 'required|string|max:30',
            'message'        => 'required|string',
            'type'           => 'nullable|string|max:30',
        ]);

        try {

            $a =  Contact::create([
                'name'           => $validated['name'],
                'email'          => $validated['email'],
                'contact_number' => $validated['number'],
                'message'        => $validated['message'],
                'type'           => $validated['type'] ?? null,
                'is_replied'     => 0,
                'soft_delete'    => 0,
            ]);
           
            return response()->json([
                'status'  => true,
                'message' => 'Your message has been sent successfully.',
            ], 201);
        } catch (\Throwable $e) {

            Log::error('Contact Store Error', [
                'error' => $e->getMessage(),
                'data'  => $request->all(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
}
