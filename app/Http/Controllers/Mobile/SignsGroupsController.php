<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\SignsGroupResource;
use App\Models\SignsGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Symfony\Component\HttpFoundation\Response;

class SignsGroupsController extends Controller
{
    /**
     * List all signs groups.
     */
    public function index(Request $request)
    {
        $request->validate([
            'created_by' => 'required|string|exists:users,name'
        ]);

        $user = User::where('name', $request['created_by'])->first();

        $groups = null;

        if ($user->hasAllPermissions(['access detailed signs', 'list detailed signs'])) {
            if ($user->can('list auth detailed signs')) {
                $groups = SignsGroupResource::collection(SignsGroup::where('created_by', $user->name)->with('signsInfo')->get());
            } else {
                $groups = SignsGroupResource::collection(SignsGroup::with('signsInfo')->get());
            }
        }

        return response()->json([
            'status' => 'success',
            'data'   => $groups
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [

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
            'signs_count'                    => 'nullable|integer|min:0',
            'columns_description'            => 'nullable|string|max:255',
            'sign_location_from_road'        => 'nullable|string|max:255',
            'sign_base'                      => 'nullable|string|max:255',
            'distance_from_road_edge_meter'  => 'nullable|numeric',
            'sign_column_radius_mm'          => 'nullable|numeric',
            'column_height'                  => 'nullable|numeric',
            'column_colour'                  => 'nullable|string|max:255',
            'column_type'                    => 'nullable|string|max:255',

            // Other details
            'comments'                       => 'nullable|string',
            'created_by'                     => 'nullable|string|max:255',
            'created_at'                     => 'required|date_format:Y-m-d H:i:s',

            // New string fields
            'image_log'                      => 'nullable|string|max:255',
            'image_lar'                      => 'nullable|string|max:255',

            // Multiple image files
            'files'                          => 'nullable|array',
            'files.*'                        => 'file|mimes:jpg,jpeg,png|max:10240',

            // Sign fields
            'signs_info' =>  [
                Rule::when($request->signs_count > 0, [
                    'required',
                    'array',
                    'size:' . $request->signs_count
                ], [
                    'nullable',
                    'array',
                    'size:0'
                ]),
            ],
            'signs_info.*.sign_name'                      => 'required|string|max:255',
            'signs_info.*.sign_code'                      => 'nullable|string|max:255',
            'signs_info.*.sign_code_gcc'                  => 'nullable|string|max:255',
            'signs_info.*.sign_type'                      => 'nullable|string|max:255',
            'signs_info.*.sign_shape'                     => 'nullable|string|max:255',
            'signs_info.*.sign_length'                    => 'nullable|numeric',
            'signs_info.*.sign_width'                     => 'nullable|numeric',
            'signs_info.*.sign_radius'                    => 'nullable|numeric',
            'signs_info.*.sign_color'                     => 'nullable|string|max:255',
            // Content
            'signs_info.*.sign_content_shape_description' => 'nullable|string|max:255',
            'signs_info.*.sign_content_arabic_text'       => 'nullable|string|max:255',
            'signs_info.*.sign_content_english_text'      => 'nullable|string|max:255',
            // Other details
            'signs_info.*.sign_condition'                 => 'nullable|string|max:255'
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

            $group = SignsGroup::create($request->except('files'));

            // Create related signs info
            if (isset($request['signs_info']) && count($request['signs_info']) > 0) {
                $group->signsInfo()->createMany($request['signs_info']);
            }

            // Attach each uploaded file to the media library
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $group->addMedia($file)
                        ->toMediaCollection('signs_groups');
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data'   => SignsGroupResource::make($group)
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

    public function addImage(Request $request, $group_id)
    {
        try {

            $group = SignsGroup::findOrFail($group_id);
            $request->validate([
                'image' => 'required|file|mimes:jpg,jpeg,png|max:10240'
            ]);

            // Check if sign already has 3 images
            if ($group->getMedia('signs_groups')->count() >= 3) {
                return response()->json([
                    'status' => 'failed',
                    'data' => 'This sign already has 3 related images, delete one atleast before adding new image.'
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($request->hasFile('image')) {
                $group->addMedia($request->file('image'))
                    ->toMediaCollection('signs_groups');

                return response()->json([
                    'status' => 'success',
                    'data' => SignsGroupResource::make(SignsGroup::findOrFail($group->id))
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'data' => 'No image uploaded.'
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteImage($group_id, $image_id)
    {
        try {
            $group = SignsGroup::findOrFail($group_id);
            $media = $group->getMedia('signs_groups')->find($image_id);

            if (!$media) {
                return response()->json([
                    'status' => 'failed',
                    'data' => 'Image not found or does not belong to this sign.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $media->delete();

            return response()->json([
                'status' => 'success',
                'data' => SignsGroupResource::make(SignsGroup::findOrFail($group->id))
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
