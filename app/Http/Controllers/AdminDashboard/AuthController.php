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
        // 1) Validate ‘name’ instead of ‘email’
        $request->validate([
            'name'     => 'required|string',
            'password' => 'required|string',
        ]);
    
        // 2) Pull in name & password
        $credentials = $request->only('name', 'password');
    
        // 3) Attempt login by username
        if (Auth::attempt($credentials)) {
            $user  = Auth::user();
            $token = $user->createToken('Teletalker_Mobile')->plainTextToken;
    
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'user'  => $user,
                    'token' => $token,
                ],
            ], Response::HTTP_OK);
        }
    
        return response()->json([
            'status'  => 'error',
            'message' => 'Invalid credentials.',
        ], Response::HTTP_BAD_REQUEST);
    }

    public function user()
    {
        $user = Auth::user();
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
