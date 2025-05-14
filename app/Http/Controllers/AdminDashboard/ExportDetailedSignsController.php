<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Exports\DetailedSignsExport;
use App\Http\Controllers\Controller;
use App\Models\DetailedSign;
use App\Models\Governorate;
use App\Models\Road;
use App\Models\Willayat;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ExportDetailedSignsController extends Controller
{
    public function exportExcel(Request $request)
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

    public function exportKML(Request $request)
    {
        try {
            // Get signs from database (add filters if needed)
            $request->validate([
                'governorate' => 'string',
                'willayat' => 'string',
                'road' => 'string'
            ]);

            $gov = Governorate::where('name_ar', $request['governorate'])->first();
            $willayat = Willayat::where('name_ar', $request['willayat'])->first();
            $road = Road::where('name', $request['road'])->first();

            $signs = null;

            // Get signs filtered
            if ($gov) {
                if ($willayat) {
                    $signs = DetailedSign::where('governorate', $gov->name_ar)
                        ->where('willayat', $willayat->name_ar)
                        ->get();
                } else {
                    $signs = DetailedSign::where('governorate', $gov->name_ar)->get();
                }
            } elseif ($road) {
                $signs = DetailedSign::where('road_name', $road->name)->get();
            } else {
                $signs = DetailedSign::all();
            }

            if (!$signs) {
                throw new \Exception("There is no signs to export.");
            }

            // Start KML content
            $kml = '<?xml version="1.0" encoding="UTF-8"?>
            <kml xmlns="http://www.opengis.net/kml/2.2">
                <Document>
                    <name>Road Signs Export</name>';

            // Add placemarks for each sign
            foreach ($signs as $sign) {
                $kml .= sprintf(
                    '
                    <Placemark>
                    <name>%s</name>
                    <description><![CDATA[
                        id: %s<br/>
                        sign_name: %s<br/>
                        sign_code: %s<br/>
                        sign_code_gcc: %s<br/>
                        sign_type: %s<br/>
                        sign_shape: %s<br/>
                        sign_length: %s<br/>
                        sign_width: %s<br/>
                        sign_radius: %s<br/>
                        sign_color: %s<br/>
                        road_classification: %s<br/>
                        road_name: %s<br/>
                        road_number: %s<br/>
                        road_type: %s<br/>
                        road_direction: %s<br/>
                        latitude: %s<br/>
                        longitude: %s<br/>
                        governorate: %s<br/>
                        willayat: %s<br/>
                        village: %s<br/>
                        signs_count: %s<br/>
                        columns_description: %s<br/>
                        sign_location_from_road: %s<br/>
                        sign_base: %s<br/>
                        distance_from_road_edge_meter: %s<br/>
                        sign_column_radius_mm: %s<br/>
                        column_height: %s<br/>
                        column_colour: %s<br/>
                        column_type: %s<br/>
                        sign_content_shape_description: %s<br/>
                        sign_content_arabic_text: %s<br/>
                        sign_content_english_text: %s<br/>
                        sign_condition: %s<br/>
                        comments: %s<br/>
                        created_by: %s<br/>
                        created_at: %s<br/>
                        updated_at: %s
                    ]]></description>
                    <Point>
                        <coordinates>%s,%s,0</coordinates>
                    </Point>
                    </Placemark>',
                    htmlspecialchars($sign->sign_name),
                    htmlspecialchars($sign->id),
                    htmlspecialchars($sign->sign_name),
                    htmlspecialchars($sign->sign_code),
                    htmlspecialchars($sign->sign_code_gcc),
                    htmlspecialchars($sign->sign_type),
                    htmlspecialchars($sign->sign_shape),
                    htmlspecialchars($sign->sign_length),
                    htmlspecialchars($sign->sign_width),
                    htmlspecialchars($sign->sign_radius),
                    htmlspecialchars($sign->sign_color),
                    htmlspecialchars($sign->road_classification),
                    htmlspecialchars($sign->road_name),
                    htmlspecialchars($sign->road_number),
                    htmlspecialchars($sign->road_type),
                    htmlspecialchars($sign->road_direction),
                    htmlspecialchars($sign->latitude),
                    htmlspecialchars($sign->longitude),
                    htmlspecialchars($sign->governorate),
                    htmlspecialchars($sign->willayat),
                    htmlspecialchars($sign->village),
                    htmlspecialchars($sign->signs_count),
                    htmlspecialchars($sign->columns_description),
                    htmlspecialchars($sign->sign_location_from_road),
                    htmlspecialchars($sign->sign_base),
                    htmlspecialchars($sign->distance_from_road_edge_meter),
                    htmlspecialchars($sign->sign_column_radius_mm),
                    htmlspecialchars($sign->column_height),
                    htmlspecialchars($sign->column_colour),
                    htmlspecialchars($sign->column_type),
                    htmlspecialchars($sign->sign_content_shape_description),
                    htmlspecialchars($sign->sign_content_arabic_text),
                    htmlspecialchars($sign->sign_content_english_text),
                    htmlspecialchars($sign->sign_condition),
                    htmlspecialchars($sign->comments),
                    htmlspecialchars($sign->created_by),
                    htmlspecialchars($sign->created_at),
                    htmlspecialchars($sign->updated_at),
                    $sign->longitude,
                    $sign->latitude
                );
            }

            $kml .= '
                </Document>
            </kml>';

            $exportedFileName = 'Signs_' . today()->format('Y-m-d') . '.kml';

            // Return as downloadable response
            return response($kml, 200, [
                'Content-Type' => 'application/vnd.google-earth.kml+xml',
                'Content-Disposition' => 'attachment; filename="' . $exportedFileName . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
