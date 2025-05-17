<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\UserTypeRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    /**
     * Register a new user and return its datails with the auth token.
     * 
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'mobile_id' => 'required|string|unique:users,mobile_id',
                // 'phone' => 'required|string|regex:/^\+?[0-9 ]+$/',
                // 'type' => ['required', new UserTypeRule()],
                'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',
                'password' => [
                    'nullable',
                    'string',
                    'min:8',                // Minimum 8 characters
                    'max:20',               // Maximum 20 characters (optional)
                    'regex:/[A-Z]/',        // Must contain at least one uppercase letter
                    'regex:/[a-z]/',        // Must contain at least one lowercase letter
                    'regex:/[0-9]/',        // Must contain at least one number
                    'regex:/[@$!%*?&#]/',   // Must contain at least one special character
                    'confirmed',            // Ensure password confirmation matches
                ]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($validator->passes()) {

                $request['type'] = 'Mobile';
                $registeredUser = User::create($request->all());

                if ($registeredUser->id) {

                    // Save the avatar if provided
                    if($request->hasFile('avatar')) {
                        $registeredUser->addMediaFromRequest('avatar')->toMediaCollection('avatars');
                    }

                    $credentials = $request->only('email', 'password');

                    // Authenticate user after registration
                    if (Auth::attempt($credentials)) {
                        $user = Auth::user();
                        $token = $user->createToken('Signs_MS_Mobile')->plainTextToken;
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
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Registration failed.'
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Login a user and return its datails with the auth token.
     * 
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
   public function login(Request $request)
{
    // 1. Validate “name” + password
    $request->validate([
        'name'     => 'required|string',
        'password' => 'required|string',
    ]);

    // 2. Grab only the name & password
    $credentials = $request->only('name', 'password');

    // 3. Attempt auth by name
    if (Auth::attempt($credentials)) {
        $user  = Auth::user();
        $token = $user->createToken('Signs_Mobile')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'user'  => $user->load('avatar'),
                'token' => $token,
            ],
        ], Response::HTTP_OK);
    }

    // 4. Failed login
    return response()->json([
        'status'  => 'error',
        'message' => 'Invalid credentials.',
    ], Response::HTTP_BAD_REQUEST);
}

    /**
     * Get the authenticated user.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function user()
    {
        $user = Auth::user();
        $user->avatar;
        return response()->json([
            'status' => 'success',
            'data' => $user
        ], Response::HTTP_OK);
    }

    /**
     * Logout the authenticated user.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'data' => 'Logged out successfully.'
        ], Response::HTTP_OK);
    }

}
