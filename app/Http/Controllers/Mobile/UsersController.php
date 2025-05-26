<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    public function index()
    {
     $users = User::all()->map(function($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => Crypt::decrypt($user->crypt_password)
        ];
    });
     return response()->json([
         'status' => 'success',
         'data' => $users,
     ], Response::HTTP_OK);
    }
}
