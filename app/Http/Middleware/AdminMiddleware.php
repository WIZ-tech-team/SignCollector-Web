<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/spa/admin/auth/login')) {
            return $this->validateLogin($request, $next);
        }

        if (!(Auth::check() && Auth::user()->type === 'Admin')) {
            return $this->unauthorizedResponse();
        }

        return $next($request);
    }

    protected function validateLogin(Request $request, Closure $next)
    {
        // 1) Validate 'name' instead of 'email'
        $request->validate([
            'name' => 'required|string|exists:users,name',
            'password' => 'required|string',
        ]);
    
        // 2) Check if this user is actually an Admin
        $user = User::where('name', $request->input('name'))->first();
    
        if (!$user || $user->type !== 'Admin') {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Login restricted to Admin users.',
            ], Response::HTTP_UNAUTHORIZED);
        }
    
        return $next($request);
    }
    protected function unauthorizedResponse()
    {
        return response()->json([
            'status' => 'failed',
            'data' => 'Unauthorized.'
        ], Response::HTTP_UNAUTHORIZED);
    }
}
