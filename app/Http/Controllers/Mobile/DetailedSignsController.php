<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedSignResource;
use App\Models\DetailedSign;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Symfony\Component\HttpFoundation\Response;

class DetailedSignsController extends Controller
{
    /**
     * List all detailed signs.
     */
    public function index(Request $request)
    {
        $request->validate([
            'created_by' => 'required|string|exists:users,name'
        ]);

        $user = User::where('name', $request['created_by'])->first();

        $signs = DetailedSignResource::collection(DetailedSign::where('created_by', $user->name)->get());

        return response()->json([
            'status' => 'success',
            'data'   => $signs,
        ], Response::HTTP_OK);
    }

    /**
     * Create a new detailed sign, with multipart image upload,
     * GPS accuracy, image location, and multiple image support.
     */
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            // Sign fields
            'sign_name'                      => 'required|string|max:255',
            'sign_code'                      => 'nullable|string|max:255',
            'sign_code_gcc'                  => 'nullable|string|max:255',
            'sign_type'                      => 'nullable|string|max:255',
            'sign_shape'                     => 'nullable|string|max:255',
            'sign_length'                    => 'nullable|numeric',
            'sign_width'                     => 'nullable|numeric',
            'sign_radius'                    => 'nullable|numeric',
            'sign_color'                     => 'nullable|string|max:255',

            // Road data
            'road_classification'            => 'nullable|string|max:255',
            'road_name'                      => 'nullable|string|max:255',
            'road_number'                    => 'nullable|string|max:255',
            'road_type'                      => 'nullable|string|max:255',
            'road_direction'                 => 'nullable|string|max:255',

            // Location
            'latitude'                       => 'required|numeric',
            'longitude'                      => 'required|numeric',
            'gps_accuracy'                   => 'nullable|numeric',
            'image_location'                 => 'nullable|string|max:255',

            'governorate'                    => 'nullable|string|max:255',
            'willayat'                       => 'nullable|string|max:255',
            'village'                        => 'nullable|string|max:255',

            // Chassis detail
            'signs_count'                    => 'nullable|integer',
            'columns_description'            => 'nullable|string|max:255',
            'sign_location_from_road'        => 'nullable|string|max:255',
            'sign_base'                      => 'nullable|string|max:255',
            'distance_from_road_edge_meter'  => 'nullable|numeric',
            'sign_column_radius_mm'          => 'nullable|numeric',
            'column_height'                  => 'nullable|numeric',
            'column_colour'                  => 'nullable|string|max:255',
            'column_type'                    => 'nullable|string|max:255',

            // Content
            'sign_content_shape_description' => 'nullable|string|max:255',
            'sign_content_arabic_text'       => 'nullable|string|max:255',
            'sign_content_english_text'      => 'nullable|string|max:255',

            // Other details
            'sign_condition'                 => 'nullable|string|max:255',
            'comments'                       => 'nullable|string',
            'created_by'                     => 'nullable|string|max:255',
            'created_at'                     => 'required|date_format:Y-m-d H:i:s',

            // New string fields
            'image_log'                      => 'nullable|string|max:255',
            'image_lar'                      => 'nullable|string|max:255',

            // Multiple image files
            'files'                          => 'nullable|array',
            'files.*'                        => 'file|mimes:jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            // Create sign record (excluding files[])
            if (isset($request['created_at'])) {
                $request['created_at'] = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $request['created_at']
                );
            }

            $sign = DetailedSign::create($request->except('files'));

            // Attach each uploaded file to the media library
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $sign->addMedia($file)
                        ->toMediaCollection('detailed_signs');
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data'   => DetailedSignResource::make($sign),
            ], Response::HTTP_CREATED);
        } catch (FileCannotBeAdded $e) {
            DB::rollBack();
            Log::error('Media upload failed: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save one or more images.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sign creation failed: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Unexpected error: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a detailed sign and its associated media.
     */
    public function destroy(DetailedSign $sign)
    {
        try {
            // Remove all media in the "detailed_signs" collection
            $sign->clearMediaCollection('detailed_signs');

            // Delete the database record
            $sign->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Sign and its media were deleted successfully.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to delete sign: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete sign.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
