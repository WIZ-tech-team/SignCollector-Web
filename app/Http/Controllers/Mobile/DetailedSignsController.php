<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedSignResource;
use App\Models\DetailedSign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class DetailedSignsController extends Controller
{
    public function index()
    {
        $signs = DetailedSignResource::collection(DetailedSign::all());

        return response()->json([
            'status' => 'success',
            'data' => $signs
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                // Sign
                'sign_name' => 'required|string|max:255',
                'sign_code' => 'string|max:255',
                'sign_code_gcc' => 'string|max:255',
                'sign_type' => 'string|max:255',
                'sign_shape' => 'string|max:255',
                'sign_length' => 'numeric',
                'sign_width' => 'numeric',
                'sign_radius' => 'numeric',
                'sign_color' => 'string|max:255',
                // Road data
                'road_classification' => 'string|max:255',
                'road_name' => 'string|max:255',
                'road_number' => 'numeric',
                'road_type' => 'string|max:255',
                'road_direction' => 'string',
                // Location
                'latitude' => 'numeric',
                'longitude' => 'numeric',
                'governorate' => 'string|max:255',
                'willayat' => 'string|max:255',
                'village' => 'string|max:255',
                // Chassis Detail
                'signs_count' => 'integer',
                'columns_description' => 'string|max:255',
                'sign_location_from_road' => 'string',
                'sign_base' => 'string|max:255',
                'distance_from_road_edge_meter' => 'numeric',
                'sign_column_radius_mm' => 'numeric',
                'column_height' => 'numeric',
                'column_colour' => 'string|max:255',
                'column_type' => 'string|max:255',
                // Content
                'sign_content_shape_description' => 'string|max:255',
                'sign_content_arabic_text' => 'string|max:255',
                'sign_content_english_text' => 'string|max:255',
                // Other Detail
                'sign_condition' => 'string|max:255',
                'comments' => 'string',
                // Image
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($validator->passes()) {

                // Create Sign
                $sign = DetailedSign::create($request->except('image'));

                // Save sign image
                if ($request->hasFile('image')) {
                    $sign->addMedia($request->file('image'))
                        ->toMediaCollection('detailed_signs');
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'data' => DetailedSignResource::make($sign)
                ], Response::HTTP_CREATED);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
