<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\SignsGroupResource;
use App\Models\SignsGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Symfony\Component\HttpFoundation\Response;

class SignsGroupsController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        $query = SignsGroup::query()->with('media');


        // 1) eager-load the entire media collection
        // 1) eager‐load the entire media collection, order by id
        if ($user->can('access detailed signs')) {
            if ($user->can('list auth detailed signs')) {
                $query = $query->where('created_by', $user->name);
            }
            $paginator = $query->orderBy('id', 'asc')
                ->paginate(SignsGroup::count());
        } else {
            return response()->json([
                'status' => 'failed',
                'data' => 'Permissions denied.'
            ], Response::HTTP_OK);
        }

        $paginator = $query->orderBy('id', 'asc')
            ->paginate(SignsGroup::count());

        // 2) transform each item with the Resource, and carry over status/meta/links
        return SignsGroupResource::collection($paginator)
            ->additional(['status' => 'success'])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(SignsGroup $group)
    {
        try {
            // If you’re using Spatie MediaLibrary and want to remove files:
            if (method_exists($group, 'clearMediaCollection')) {
                $group->clearMediaCollection('signs_groups');
            }

            // Delete the database record
            $group->delete();

            return response()->json([
                'message' => 'Sign deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Admin delete DetailedSign failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete signs group.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $group_id)
    {
        // 1) Find the sign or fail
        $group = SignsGroup::findOrFail($group_id);

        // 2) Validate exactly the same fields as store
        $validator = Validator::make($request->all(), [

            'gps_accuracy'                   => 'nullable|numeric',
            'image_location'                 => 'nullable|string|max:255',

            'road_classification'            => 'nullable|string|max:255',
            'road_name'                      => 'nullable|string|max:255',
            'road_number'                    => 'nullable|string|max:255',
            'road_type'                      => 'nullable|string|max:255',
            'road_direction'                 => 'nullable|string|max:255',
            'latitude'                       => 'required|numeric',
            'longitude'                      => 'required|numeric',
            'gps_accuracy'                   => 'nullable|numeric',
            'image_location'                 => 'nullable|string|max:255',
            'governorate'                    => 'nullable|string|max:255',
            'willayat'                       => 'nullable|string|max:255',
            'village'                        => 'nullable|string|max:255',
            'signs_count'                    => 'nullable|integer',
            'columns_description'            => 'nullable|string|max:255',
            'sign_location_from_road'        => 'nullable|string|max:255',
            'sign_base'                      => 'nullable|string|max:255',
            'distance_from_road_edge_meter'  => 'nullable|numeric',
            'sign_column_radius_mm'          => 'nullable|numeric',
            'column_height'                  => 'nullable|numeric',
            'column_colour'                  => 'nullable|string|max:255',
            'column_type'                    => 'nullable|string|max:255',

            'comments'                       => 'nullable|string',
            'created_by'                     => 'nullable|string|max:255',
            'image_log'                      => 'nullable|string|max:255',
            'image_lar'                      => 'nullable|string|max:255',
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
            'signs_info.*.sign_name'                      => 'required_with:signs_info|string|max:255',
            'signs_info.*.sign_custom_name'               => 'required_with:signs_info|string|max:255',
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
            // 3) Update the sign's attributes
            $group->update($request->except(['files', 'signs_info']));

            // 4) Handle any new file uploads
            if ($request->hasFile('files')) {
                // Remove old images
                $group->clearMediaCollection('signs_groups');

                // Attach each uploaded file
                foreach ($request->file('files') as $file) {
                    $group
                        ->addMedia($file)
                        ->toMediaCollection('signs_groups');
                }
            }

            // 5) Handle related signs info
            if ($request->has('signs_info')) {
                
                // Sets the sign name, in case of free sign name
                // When the sign_name is chosen, the sign_custom_name will have the same value in the front
                $request['sign_name'] = $request['sign_custom_name'];

                $currentSignsNames = collect($request->signs_info)->pluck('sign_name');

                // Delete signs_info not in the request
                $group->signsInfo()
                    ->whereNotIn('sign_name', $currentSignsNames)
                    ->delete();

                // Update or create signsInfo
                foreach ($request->signs_info as $signInfo) {
                    $group->signsInfo()->updateOrCreate(
                        [
                            'sign_name' => $signInfo['sign_name'],
                            'signs_group_id' => $group->id
                        ],
                        $signInfo
                    );
                }
            } else {
                $group->signsInfo()->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data'   => SignsGroupResource::make($group->fresh())
            ], Response::HTTP_OK);
        } catch (FileCannotBeAdded $e) {
            DB::rollBack();
            Log::error('Media upload failed on update: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save one or more images.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sign update failed: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Unexpected error: ' . $e->getMessage(),
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
                    'data' => 'Image not found or does not belong to this signs group.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $media->delete();

            return response()->json([
                'status' => 'success',
                'data' => SignsGroupResource::make($group->fresh())
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
