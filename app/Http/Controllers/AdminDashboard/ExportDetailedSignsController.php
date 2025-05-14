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
                    <name>Road Signs Export</name>
                    <!-- Style for Left Bend (Warning Sign, Triangle) -->
                    <Style id="leftBendStyle">
                        <IconStyle>
                            <scale>1.2</scale>
                            <Icon>
                                <href>https://maps.google.com/mapfiles/kml/shapes/warning.png</href>
                            </Icon>
                        </IconStyle>
                        <LabelStyle>
                            <color>ffffffff</color>
                            <scale>1.0</scale>
                        </LabelStyle>
                    </Style>
                    <!-- Style for N/A Signs (Generic Marker) -->
                    <Style id="defaultSignStyle">
                        <IconStyle>
                            <scale>1.0</scale>
                            <Icon>
                                <href>https://maps.google.com/mapfiles/kml/paddle/red-circle.png</href>
                            </Icon>
                        </IconStyle>
                        <LabelStyle>
                            <color>ffffffff</color>
                            <scale>1.0</scale>
                        </LabelStyle>
                    </Style>';

            // Add placemarks for each sign
            foreach ($signs as $sign) {
                $kml .= sprintf(
                    '
                    <Placemark>
                    <name>%s</name>
                    <description><![CDATA[
                        <b>ID:</b> %s<br/>
                        <b>Sign Name:</b> %s<br/>
                        <b>Sign Code:</b> %s<br/>
                        <b>Sign Code gcc:</b> %s<br/>
                        <b>Sign Type:</b> %s<br/>
                        <b>Sign Shape:</b> %s<br/>
                        <b>Sign Length:</b> %s<br/>
                        <b>Sign Width:</b> %s<br/>
                        <b>Sign Radius:</b> %s<br/>
                        <b>Sign Color:</b> %s<br/>
                        <b>Road Classification:</b> %s<br/>
                        <b>Road Name:</b> %s<br/>
                        <b>Road Number:</b> %s<br/>
                        <b>Road Type:</b> %s<br/>
                        <b>Road Direction:</b> %s<br/>
                        <b>Latitude:</b> %s<br/>
                        <b>Longitude:</b> %s<br/>
                        <b>Governorate:</b> %s<br/>
                        <b>Willayat:</b> %s<br/>
                        <b>Village:</b> %s<br/>
                        <b>Signs Count:</b> %s<br/>
                        <b>Columns Description:</b> %s<br/>
                        <b>Sign Location from Road:</b> %s<br/>
                        <b>Sign Base:</b> %s<br/>
                        <b>distance from Road Edge meter:</b> %s<br/>
                        <b>Sign Column Radius mm:</b> %s<br/>
                        <b>Column Height:</b> %s<br/>
                        <b>Column Colour:</b> %s<br/>
                        <b>Column Type:</b> %s<br/>
                        <b>Sign Content Shape Description:</b> %s<br/>
                        <b>Sign Content Arabic Text:</b> %s<br/>
                        <b>Sign Content English Text:</b> %s<br/>
                        <b>Sign Condition:</b> %s<br/>
                        <b>Comments:</b> %s<br/>
                        <b>Created by:</b> %s<br/>
                        <b>Created at:</b> %s<br/>
                        <b>Updated at:</b> %s
                    ]]></description>
                    <Point>
                        <coordinates>%s,%s,0</coordinates>
                    </Point>
                    </Placemark>',
                    htmlspecialchars($sign->sign_name ?? 'Unknown Sign (ID ' . $sign->id . ')'),
                    htmlspecialchars($sign->id),
                    htmlspecialchars($sign->sign_name ?? 'N/A'),
                    htmlspecialchars($sign->sign_code ?? 'N/A'),
                    htmlspecialchars($sign->sign_code_gcc ?? 'N/A'),
                    htmlspecialchars($sign->sign_type ?? 'N/A'),
                    htmlspecialchars($sign->sign_shape ?? 'N/A'),
                    htmlspecialchars($sign->sign_length ?? 'N/A'),
                    htmlspecialchars($sign->sign_width ?? 'N/A'),
                    htmlspecialchars($sign->sign_radius ?? 'N/A'),
                    htmlspecialchars($sign->sign_color ?? 'N/A'),
                    htmlspecialchars($sign->road_classification ?? 'N/A'),
                    htmlspecialchars($sign->road_name ?? 'N/A'),
                    htmlspecialchars($sign->road_number ?? 'N/A'),
                    htmlspecialchars($sign->road_type ?? 'N/A'),
                    htmlspecialchars($sign->road_direction ?? 'N/A'),
                    htmlspecialchars($sign->latitude ?? 'N/A'),
                    htmlspecialchars($sign->longitude ?? 'N/A'),
                    htmlspecialchars($sign->governorate ?? 'N/A'),
                    htmlspecialchars($sign->willayat ?? 'N/A'),
                    htmlspecialchars($sign->village ?? 'N/A'),
                    htmlspecialchars($sign->signs_count ?? 'N/A'),
                    htmlspecialchars($sign->columns_description ?? 'N/A'),
                    htmlspecialchars($sign->sign_location_from_road ?? 'N/A'),
                    htmlspecialchars($sign->sign_base ?? 'N/A'),
                    htmlspecialchars($sign->distance_from_road_edge_meter ?? 'N/A'),
                    htmlspecialchars($sign->sign_column_radius_mm ?? 'N/A'),
                    htmlspecialchars($sign->column_height ?? 'N/A'),
                    htmlspecialchars($sign->column_colour ?? 'N/A'),
                    htmlspecialchars($sign->column_type ?? 'N/A'),
                    htmlspecialchars($sign->sign_content_shape_description ?? 'N/A'),
                    htmlspecialchars($sign->sign_content_arabic_text ?? 'N/A'),
                    htmlspecialchars($sign->sign_content_english_text ?? 'N/A'),
                    htmlspecialchars($sign->sign_condition ?? 'N/A'),
                    htmlspecialchars($sign->comments ?? 'N/A'),
                    htmlspecialchars($sign->created_by ?? 'N/A'),
                    htmlspecialchars($sign->created_at ?? 'N/A'),
                    htmlspecialchars($sign->updated_at ?? 'N/A'),
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

    public function exportShapefile(Request $request)
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
                return response()->json([
                    'status' => 'failed',
                    'data' => 'There is no signs to export.'
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'status' => 'success',
                'data' => $signs
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
