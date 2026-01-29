<?php

namespace App\Http\Controllers\API;

use auth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);
        
        // handle error
        if (!$user) {
            return response()->json([
                'message' => 'Register failed'
            ], 500);
        }

        return response()->json([
            'code' => 201,
            'message' => 'Register success',
            'data' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid username or password'
            ], 401);
        }

        return response()->json([
            'code' => 200,
            'token' => $token,
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
