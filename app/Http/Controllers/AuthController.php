<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWT;

class AuthController extends Controller
{
    public function __construct(private JWT $jwt)
    {
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (! $token) {
            return response()->json([
                'message' => 'Email or Password is not correct',
            ], 400);
        }

        $user = Auth::user();

        return response()->json([
            'data' => [
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                    'expiresIn' => Auth::factory()->getTTL() * 60,

                ],
            ],
        ]);

    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);

        return response()->json([
            'message' => 'User created successfully',
            'data' => [
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                    'expiresIn' => Auth::factory()->getTTL() * 60,
                ],
            ],

        ]);
    }

    public function logout()
    {
        $this->jwt->invalidate(true);

        return response()->json(null, 204);
    }

    public function refresh()
    {
        return response()->json([
            'data' => [
                'user' => Auth::user(),
                'authorisation' => [
                    'token' => Auth::refresh(),
                    'type' => 'bearer',
                ],
            ],

        ]);
    }
}
