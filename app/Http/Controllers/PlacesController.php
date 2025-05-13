<?php

namespace App\Http\Controllers;

use App\Models\Governorate;
use App\Models\Road;
use App\Models\Village;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlacesController extends Controller
{
    public function governoratesList()
    {
        $list = Governorate::with('willayats')->get();

        return response()->json([
            'status' => 'success',
            'data' => $list
        ], Response::HTTP_OK);
    }

    public function villagesList()
    {
        $list = Village::all();

        return response()->json([
            'status' => 'success',
            'data' => $list
        ], Response::HTTP_OK);
    }

    public function roadsList()
    {
        $list = Road::all();

        return response()->json([
            'status' => 'success',
            'data' => $list
        ], Response::HTTP_OK);
    }
}
