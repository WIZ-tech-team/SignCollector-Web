<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Sign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SignsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $signs = Sign::with('image', 'location', 'roadData', 'chassisDetail', 'content', 'otherDetail')->get();
        return response()->json([
            'status' => 'success',
            'data' => $signs
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                // Sign
                'name' => 'required|string|max:255',
                'code_2010' => 'required|string|max:255',
                'code_gcc' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'shape' => 'required|string|max:255',
                'length' => 'required|numeric',
                'width' => 'required|numeric',
                'background_radius' => 'required|numeric',
                'background_color' => 'required|string|max:255',
                // Road data
                'classification' => 'required|string|max:255',
                'road_name' => 'required|string|max:255',
                'road_number' => 'required|numeric',
                'road_type' => 'required|string|max:255',
                'road_direction' => 'required|string',
                // Location
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'governorate' => 'required|string|max:255',
                'willayat' => 'required|string|max:255',
                'village' => 'required|string|max:255',
                // Chassis Detail
                'count' => 'required|integer',
                'columns_description' => 'required|string|max:255',
                'location_from_road' => 'required|string',
                'base' => 'required|string|max:255',
                'disatance_from_road_edge' => 'required|numeric',
                'column_radius' => 'required|numeric',
                'column_height' => 'required|numeric',
                'column_color' => 'required|string|max:255',
                'column_type' => 'required|string|max:255',
                // Content
                'shape_description' => 'required|string|max:255',
                'written_content_en' => 'required|string|max:255',
                'written_content_ar' => 'required|string|max:255',
                // Other Detail
                'condition' => 'required|string|max:255',
                'comments' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($validator->passes()) {

                // Create Sign
                $sign = Sign::create($request->only([
                    'name',
                    'code_2010',
                    'code_gcc',
                    'type',
                    'shape',
                    'length',
                    'width',
                    'background_radius',
                    'background_color'
                ]));

                // Save sign image
                if ($request->hasFile('image')) {
                    $sign->addMedia($request->file('image'))
                        ->toMediaCollection('signs');
                }

                // Save sign location
                $sign->location()->create($request->only(['latitude', 'longitude', 'governorate', 'willayat', 'village']));

                // Save sign road data
                $sign->roadData()->create([
                    'classification' => $request['classification'],
                    'name' => $request['road_name'],
                    'number' => $request['road_number'],
                    'type' => $request['road_type'],
                    'direction' => $request['road_direction']
                ]);

                // Save sign chassis
                $sign->chassisDetail()->create($request->only([
                    'count',
                    'columns_description',
                    'location_from_road',
                    'base',
                    'disatance_from_road_edge',
                    'column_radius',
                    'column_height',
                    'column_color',
                    'column_type'
                ]));

                // Save sign content
                $sign->content()->create([
                    'shape_description' => $request['shape_description'],
                    'written_content' => ['en' => $request['written_content_en'], 'ar' => $request['written_content_ar']]
                ]);

                // Save sign other detail
                $sign->otherDetail()->create($request->only(['condition', 'comments']));

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'data' => $sign->load('image', 'location', 'roadData', 'chassisDetail', 'content', 'otherDetail')
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
