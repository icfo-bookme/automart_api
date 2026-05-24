<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    // REGISTER
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'phone'    => 'required|string|unique:users',
            'nid'      => 'required|string|unique:users',
            'country'  => 'required|string',
            'district' => 'required|string',
            'city'     => 'required|string',
            'thana'    => 'required|string',
            'area'     => 'required|string',
            'address'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'phone'      => $request->phone,
            'nid'        => $request->nid,
            'country'    => $request->country,
            'district'   => $request->district,
            'city'       => $request->city,
            'thana'      => $request->thana,
            'area'       => $request->area,
            'address'    => $request->address,
        ]);

        // Log the user in immediately after registration
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user'    => $user,
        ], 201);
    }


    // LOGIN  (Sanctum SPA / session)
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        if (! Auth::guard('web')->attempt(
            $request->only('phone', 'password'),
            $request->boolean('remember')
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'user'    => Auth::user(),
        ]);
    }


    // LOGOUT
    public function logout(Request $request): JsonResponse
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    // GET AUTH USER
    public function user(Request $request): JsonResponse
    {
        if (! $request->user()) {
            return response()->json([
                'success' => false,
                'user'    => null,
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user'    => $request->user(),
        ]);
    }


    // UPDATE PROFILE
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|unique:users,email,' . $user->id,
            'phone'      => 'sometimes|string|unique:users,phone,' . $user->id,
            'country'    => 'sometimes|string',
            'district'   => 'sometimes|string',
            'city'       => 'sometimes|string',
            'thana'      => 'sometimes|string',
            'area'       => 'sometimes|string',
            'road_no'    => 'sometimes|nullable|string',
            'house_no'   => 'sometimes|nullable|string',
            'flat_no'    => 'sometimes|nullable|string',
            'address'    => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Only update fillable fields — never accept password here
        $user->update($request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'country',
            'district',
            'city',
            'thana',
            'area',
            'road_no',
            'house_no',
            'flat_no',
            'address',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated',
            'user'    => $user->fresh(),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(6)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // ❌ Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 400);
        }

        // 🔐 Update password
        $user->password = Hash::make($request->password);
        $user->save();
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Password updated successfully',
            'user' => $user,
        ]);
    }
}
