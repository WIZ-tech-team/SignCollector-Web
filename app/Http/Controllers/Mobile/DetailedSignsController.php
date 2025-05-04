<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedSignResource;
use App\Models\DetailedSign;
use App\Rules\Base64Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
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
                'road_number' => 'string|max:255',
                'road_type' => 'string|max:255',
                'road_direction' => 'string',
                // Location
                'latitude' => 'numeric|required',
                'longitude' => 'numeric|required',
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
                'image' => ['required', new Base64Image],
                'image_name' => 'required|string|max:255'
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
                try {
                    // Extract the image type from base64 string
                    preg_match('/^data:image\/(\w+);base64,/', $request['image'], $matches);
                    $extension = $matches[1] ?? 'png'; // Default to png if no match
                    
                    // Clean up the filename (remove existing extension if present)
                    $filename = pathinfo($request['image_name'], PATHINFO_FILENAME);
                    
                    $sign->addMediaFromBase64($request['image'])
                        ->usingFileName("{$filename}.{$extension}")  // Force correct extension
                        ->withCustomProperties(['mime_type' => "image/{$extension}"]) // Set MIME type
                        ->toMediaCollection('detailed_signs');
                        
                } catch (FileCannotBeAdded $e) {
                    throw new \Exception("Failed to add media: " . $e->getMessage());
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
