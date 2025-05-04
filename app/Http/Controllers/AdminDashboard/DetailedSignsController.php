<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedSignResource;
use App\Models\DetailedSign;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetailedSignsController extends Controller
{
    public function index()
    {
        $signs = DetailedSign::with('image')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $signs
        ], Response::HTTP_OK);
    }
}
