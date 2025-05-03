<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('Teletalker_Mobile')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $user->load('avatar'),
                    'token' => $token
                ]
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function user()
    {
        $user = Auth::user();
        $user->avatar;
        return response()->json([
            'status' => 'success',
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'data' => 'Logged out successfully.'
        ], Response::HTTP_OK);
    }
}
