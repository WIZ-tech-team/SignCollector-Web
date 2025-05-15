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
use Shapefile\Geometry\Point;
use Shapefile\Shapefile;
use Shapefile\ShapefileWriter;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

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

            if (!count($signs) > 0) {
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

        if (!count($signs) > 0) {
            throw new \Exception("There is no signs to export.");
        }

        try {
            // Create temporary directory
            $tempDir = storage_path('app/temp/shp_' . uniqid());
            mkdir($tempDir, 0755, true);

            // Initialize Shapefile writer
            $shapefile = new ShapefileWriter($tempDir . '/road_signs.shp');

            $shapefile->setShapeType(Shapefile::SHAPE_TYPE_POINT);

            // Add fields (adjust according to your model)
            $shapefile->addCharField('id');
            $shapefile->addCharField('name');
            $shapefile->addCharField('code');
            $shapefile->addCharField('codeGcc');
            $shapefile->addCharField('type');
            $shapefile->addCharField('shape');
            $shapefile->addCharField('length');
            $shapefile->addCharField('width');
            $shapefile->addCharField('radius');
            $shapefile->addCharField('color');
            $shapefile->addCharField('class');
            $shapefile->addCharField('rName');
            $shapefile->addCharField('rNum');
            $shapefile->addCharField('rType');
            $shapefile->addCharField('rDir');
            $shapefile->addCharField('latitude');
            $shapefile->addCharField('longitude');
            $shapefile->addCharField('gov');
            $shapefile->addCharField('willayat');
            $shapefile->addCharField('village');
            $shapefile->addCharField('signsCount');
            $shapefile->addCharField('colDesc');
            $shapefile->addCharField('location');
            $shapefile->addCharField('base');
            $shapefile->addCharField('distance');
            $shapefile->addCharField('colRadius');
            $shapefile->addCharField('colHeight');
            $shapefile->addCharField('colColor');
            $shapefile->addCharField('colType');
            $shapefile->addCharField('contDesc');
            $shapefile->addCharField('arContent');
            $shapefile->addCharField('enContent');
            $shapefile->addCharField('condition');
            $shapefile->addCharField('comments');
            $shapefile->addCharField('createdBy');
            $shapefile->addCharField('createdAt');
            $shapefile->addCharField('updatedAt');

            // Add records
            foreach ($signs as $sign) {

                $point = new Point($sign->longitude, $sign->latitude);
                $point->setData('id', $sign->id);
                $point->setData('name', $sign->sign_name ?? 'N/A');
                $point->setData('code', $sign->sign_code ?? 'N/A');
                $point->setData('codeGcc', $sign->sign_code_gcc ?? 'N/A');
                $point->setData('type', $sign->sign_type ?? 'N/A');
                $point->setData('shape', $sign->sign_shape ?? 'N/A');
                $point->setData('length', $sign->sign_length ?? 'N/A');
                $point->setData('width', $sign->sign_width ?? 'N/A');
                $point->setData('radius', $sign->sign_radius ?? 'N/A');
                $point->setData('color', $sign->sign_color ?? 'N/A');
                $point->setData('class', $sign->road_classification ?? 'N/A');
                $point->setData('rName', $sign->road_name ?? 'N/A');
                $point->setData('rNum', $sign->road_number ?? 'N/A');
                $point->setData('rType', $sign->road_type ?? 'N/A');
                $point->setData('rDir', $sign->road_direction ?? 'N/A');
                $point->setData('latitude', $sign->latitude ?? 'N/A');
                $point->setData('longitude', $sign->longitude ?? 'N/A');
                $point->setData('gov', $sign->governorate ?? 'N/A');
                $point->setData('willayat', $sign->willayat ?? 'N/A');
                $point->setData('village', $sign->village ?? 'N/A');
                $point->setData('signsCount', $sign->signs_count ?? 'N/A');
                $point->setData('colDesc', $sign->columns_description ?? 'N/A');
                $point->setData('location', $sign->sign_location_from_road ?? 'N/A');
                $point->setData('base', $sign->sign_base ?? 'N/A');
                $point->setData('distance', $sign->distance_from_road_edge_meter ?? 'N/A');
                $point->setData('colRadius', $sign->sign_column_radius_mm ?? 'N/A');
                $point->setData('colHeight', $sign->column_height ?? 'N/A');
                $point->setData('colColor', $sign->column_colour ?? 'N/A');
                $point->setData('colType', $sign->column_type ?? 'N/A');
                $point->setData('contDesc', $sign->sign_content_shape_description ?? 'N/A');
                $point->setData('arContent', $sign->sign_content_arabic_text ?? 'N/A');
                $point->setData('enContent', $sign->sign_content_english_text ?? 'N/A');
                $point->setData('condition', $sign->sign_condition ?? 'N/A');
                $point->setData('comments', $sign->comments ?? 'N/A');
                $point->setData('createdBy', $sign->created_by ?? 'N/A');
                $point->setData('createdAt', $sign->created_at ?? 'N/A');
                $point->setData('updatedAt', $sign->updated_at ?? 'N/A');

                $shapefile->writeRecord($point);
            }

            // Close the shapefile writer to ensure all files are properly written
            // unset($shapefile);
            $shapefile = null;

            // Add PRJ file (WGS84)
            // file_put_contents(
            //     $tempDir . '/road_signs.prj',
            //     'GEOGCS["WGS 84",DATUM["WGS_1984",
            //     SPHEROID["WGS 84",6378137,298.257223563]],
            //     PRIMEM["Greenwich",0],UNIT["degree",0.0174532925199433]]'
            // );

            // Create ZIP archive
            $zipFile = $tempDir . '/road_signs.zip';
            $zip = new ZipArchive();
            $zip->open($zipFile, ZipArchive::CREATE);

            foreach (glob($tempDir . '/road_signs.*') as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->addFile(public_path('storage/road_signs.prj'), 'road_signs.prj');

            $zip->close();

            // Stream the ZIP file
            // return response()->download($zipFile, 'road_signs.zip');

            $exportedFileName = 'Signs_' . today()->format('Y-m-d') . '.zip';

            return response()->download($zipFile, $exportedFileName, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $exportedFileName . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Shapefile export failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
