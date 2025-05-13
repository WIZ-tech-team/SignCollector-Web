<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Exports\DetailedSignsExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedSignResource;
use App\Models\DetailedSign;
use App\Models\Governorate;
use App\Models\Road;
use App\Models\Willayat;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Illuminate\Support\Facades\Validator;      // ← add this line

use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Symfony\Component\HttpFoundation\Response;

class DetailedSignsController extends Controller
{

    public function index()
    {
        // 1) eager-load the entire media collection
        // 1) eager‐load the entire media collection, order by id
        $paginator = DetailedSign::with('media')
            ->orderBy('id', 'asc')
            ->paginate(DetailedSign::count());

        // 2) transform each item with the Resource, and carry over status/meta/links
        return DetailedSignResource::collection($paginator)
            ->additional(['status' => 'success'])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(DetailedSign $sign)
    {
        try {
            // If you’re using Spatie MediaLibrary and want to remove files:
            if (method_exists($sign, 'clearMediaCollection')) {
                $sign->clearMediaCollection('detailed_signs');
            }

            // Delete the database record
            $sign->delete();

            return response()->json([
                'message' => 'Sign deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Admin delete DetailedSign failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete sign.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'governorate' => 'string',
            'willayat' => 'string',
            'road' => 'string'
        ]);

        $gov = Governorate::where('name_ar', $request['governorate'])->first();
        $willayat = Willayat::where('name_ar', $request['willayat'])->first();
        $road = Road::where('name', $request['road'])->first();

        return Excel::download(
            new DetailedSignsExport($gov, $willayat, $road),
            'Signs_' . today()->format('Y-m-d') . '.xlsx', // Ensure extension here
            ExcelExcel::XLSX
        );
    }
    
    public function update(Request $request, $id)
    {
        // 1) Find the sign or fail
        $sign = DetailedSign::findOrFail($id);

        // 2) Validate exactly the same fields as store
        $validator = Validator::make($request->all(), [
            'sign_name'                      => 'required|string|max:255',
            'sign_code'                      => 'nullable|string|max:255',
            'sign_code_gcc'                  => 'nullable|string|max:255',
            'sign_type'                      => 'nullable|string|max:255',
            'sign_shape'                     => 'nullable|string|max:255',
            'sign_length'                    => 'nullable|numeric',
            'sign_width'                     => 'nullable|numeric',
            'sign_radius'                    => 'nullable|numeric',
            'sign_color'                     => 'nullable|string|max:255',
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
            'sign_content_shape_description' => 'nullable|string|max:255',
            'sign_content_arabic_text'       => 'nullable|string|max:255',
            'sign_content_english_text'      => 'nullable|string|max:255',
            'sign_condition'                 => 'nullable|string|max:255',
            'comments'                       => 'nullable|string',
            'created_by'                     => 'nullable|string|max:255',
            'image_log'                      => 'nullable|string|max:255',
            'image_lar'                      => 'nullable|string|max:255',
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
            // 3) Update the sign's attributes
            $sign->update($request->except('files'));

            // 4) Handle any new file uploads
            if ($request->hasFile('files')) {
                // Remove old images
                $sign->clearMediaCollection('detailed_signs');

                // Attach each uploaded file
                foreach ($request->file('files') as $file) {
                    $sign
                        ->addMedia($file)
                        ->toMediaCollection('detailed_signs');
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data'   => DetailedSignResource::make($sign->fresh()),
            ], Response::HTTP_OK);
        } catch (FileCannotBeAdded $e) {
            DB::rollBack();
            Log::error('Media upload failed on update: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save one or more images.',
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
}
