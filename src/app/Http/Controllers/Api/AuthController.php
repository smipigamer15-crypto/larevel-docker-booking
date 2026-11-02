<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function token(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|max:4096',
            'device_name' => 'required|string|max:255',
            'abilities' => 'array',
            'abilities.*' => 'string|in:bookings:read,bookings:write,services:read,profile:read,profile:write',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        abort_if($user->two_factor_enabled, 403, 'Sign in using the two-factor authentication flow.');

        $user->tokens()->where('name', $request->device_name)->delete();

        $abilities = $request->input('abilities', [
            'bookings:read',
            'bookings:write',
            'services:read',
            'profile:read',
            'profile:write',
        ]);

        $token = $user->createToken($request->device_name, $abilities);

        return response()->json([
            'success' => true,
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'abilities' => $abilities,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function revoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token revoked successfully.',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
            ],
            'abilities' => $request->user()->currentAccessToken()->abilities ?? [],
        ]);
    }
}
