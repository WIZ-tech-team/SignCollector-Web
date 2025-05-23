<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Exports\SignsGroupsExport;
use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Road;
use App\Models\SignsGroup;
use App\Models\Willayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Shapefile\Geometry\Point;
use Shapefile\Shapefile;
use Shapefile\ShapefileWriter;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class ExportSignsGroupsController extends Controller
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
            new SignsGroupsExport($gov, $willayat, $road),
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

            $user = Auth::user();
            $query = SignsGroup::query()->with('signsInfo');

            if ($user->can('access detailed signs')) {
                if ($user->can('list auth detailed signs')) {
                    $query = $query->where('created_by', $user->name);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'data' => 'Permissions denied.'
                ], Response::HTTP_OK);
            }

            $gov = Governorate::where('name_ar', $request['governorate'])->first();
            $willayat = Willayat::where('name_ar', $request['willayat'])->first();
            $road = Road::where('name', $request['road'])->first();

            $groups = null;

            // Get signs filtered
            if ($gov) {
                if ($willayat) {
                    $groups = $query->where('governorate', $gov->name_ar)
                        ->where('willayat', $willayat->name_ar)
                        ->get();
                } else {
                    $groups = $query->where('governorate', $gov->name_ar)->get();
                }
            } elseif ($road) {
                $groups = $query->where('road_name', $road->name)->get();
            } else {
                $groups = $query->get();
            }

            if (!count($groups) > 0) {
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
            foreach ($groups as $group) {
                // Build the description content dynamically
                $description = '
                <Placemark>
                <description><![CDATA[
                <b>ID:</b> ' . htmlspecialchars($group->id) . '<br/>
                <b>Road Classification:</b> ' . htmlspecialchars($group->road_classification ?? 'N/A') . '<br/>
                <b>Road Name:</b> ' . htmlspecialchars($group->road_name ?? 'N/A') . '<br/>
                <b>Road Number:</b> ' . htmlspecialchars($group->road_number ?? 'N/A') . '<br/>
                <b>Road Type:</b> ' . htmlspecialchars($group->road_type ?? 'N/A') . '<br/>
                <b>Road Direction:</b> ' . htmlspecialchars($group->road_direction ?? 'N/A') . '<br/>
                <b>Latitude:</b> ' . htmlspecialchars($group->latitude ?? 'N/A') . '<br/>
                <b>Longitude:</b> ' . htmlspecialchars($group->longitude ?? 'N/A') . '<br/>
                <b>Governorate:</b> ' . htmlspecialchars($group->governorate ?? 'N/A') . '<br/>
                <b>Willayat:</b> ' . htmlspecialchars($group->willayat ?? 'N/A') . '<br/>
                <b>Village:</b> ' . htmlspecialchars($group->village ?? 'N/A') . '<br/>
                <b>Signs Count:</b> ' . htmlspecialchars($group->signs_count ?? 'N/A') . '<br/>
                <b>Columns Description:</b> ' . htmlspecialchars($group->columns_description ?? 'N/A') . '<br/>
                <b>Sign Location from Road:</b> ' . htmlspecialchars($group->sign_location_from_road ?? 'N/A') . '<br/>
                <b>Sign Base:</b> ' . htmlspecialchars($group->sign_base ?? 'N/A') . '<br/>
                <b>distance from Road Edge meter:</b> ' . htmlspecialchars($group->distance_from_road_edge_meter ?? 'N/A') . '<br/>
                <b>Sign Column Radius mm:</b> ' . htmlspecialchars($group->sign_column_radius_mm ?? 'N/A') . '<br/>
                <b>Column Height:</b> ' . htmlspecialchars($group->column_height ?? 'N/A') . '<br/>
                <b>Column Colour:</b> ' . htmlspecialchars($group->column_colour ?? 'N/A') . '<br/>
                <b>Column Type:</b> ' . htmlspecialchars($group->column_type ?? 'N/A') . '<br/>
                <b>Comments:</b> ' . htmlspecialchars($group->comments ?? 'N/A') . '<br/>
                <b>Created by:</b> ' . htmlspecialchars($group->created_by ?? 'N/A') . '<br/>
                <b>Created at:</b> ' . htmlspecialchars($group->created_at ?? 'N/A') . '<br/>
                <b>Updated at:</b> ' . htmlspecialchars($group->updated_at ?? 'N/A') . '<br/>
                ';

                if (count($group->signsInfo) > 0) {
                    foreach ($group->signsInfo as $key => $signInfo) {
                        $order = $key + 1;
                        $description .= '
                    <b>Sign (' . $order . ') Name:</b> ' . htmlspecialchars($signInfo->sign_name ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Code:</b> ' . htmlspecialchars($signInfo->sign_code ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Code:</b> ' . htmlspecialchars($signInfo->sign_code_gcc ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Type:</b> ' . htmlspecialchars($signInfo->sign_type ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Shape:</b> ' . htmlspecialchars($signInfo->sign_shape ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Length:</b> ' . htmlspecialchars($signInfo->sign_length ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Width:</b> ' . htmlspecialchars($signInfo->sign_width ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Radius:</b> ' . htmlspecialchars($signInfo->sign_radius ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Color:</b> ' . htmlspecialchars($signInfo->sign_color ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Content Shape Description:</b> ' . htmlspecialchars($signInfo->sign_content_shape_description ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Content Arabic Text:</b> ' . htmlspecialchars($signInfo->sign_content_arabic_text ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Content English Text:</b> ' . htmlspecialchars($signInfo->sign_content_english_text ?? 'N/A') . '<br/>
                    <b>Sign (' . $order . ') Condition:</b> ' . htmlspecialchars($signInfo->sign_condition ?? 'N/A') . '<br/>
                    ';

                        $kml .= $description . ']]></description>';
                    }
                    $kml .= '
                        <Point>
                            <coordinates>' . $group->longitude . ',' . $group->latitude . ',0</coordinates>
                        </Point>
                    </Placemark>
                    ';
                } else {
                    $kml .= $description . ']]></description>
                        <Point>
                            <coordinates>' . $group->longitude . ',' . $group->latitude . ',0</coordinates>
                        </Point>
                    </Placemark>
                    ';
                }
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

        $user = Auth::user();
        $query = SignsGroup::query()->with('signsInfo');

        if ($user->can('access detailed signs')) {
            if ($user->can('list auth detailed signs')) {
                $query = $query->where('created_by', $user->name);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'data' => 'Permissions denied.'
            ], Response::HTTP_OK);
        }

        $gov = Governorate::where('name_ar', $request['governorate'])->first();
        $willayat = Willayat::where('name_ar', $request['willayat'])->first();
        $road = Road::where('name', $request['road'])->first();

        $groups = null;

        // Get signs filtered
        if ($gov) {
            if ($willayat) {
                $groups = $query->where('governorate', $gov->name_ar)
                    ->where('willayat', $willayat->name_ar)
                    ->get();
            } else {
                $groups = $query->where('governorate', $gov->name_ar)->get();
            }
        } elseif ($road) {
            $groups = $query->where('road_name', $road->name)->get();
        } else {
            $groups = $query->get();
        }

        if (!count($groups) > 0) {
            throw new \Exception("There is no signs to export.");
        }

        $signsInfoMaxLength = $groups->max(function ($item) {
            return count($item->signsInfo);
        });

        try {
            // Create temporary directory
            $tempDir = storage_path('app/temp/shp_' . uniqid());
            mkdir($tempDir, 0755, true);

            // Initialize Shapefile writer
            $shapefile = new ShapefileWriter($tempDir . '/road_signs.shp');

            $shapefile->setShapeType(Shapefile::SHAPE_TYPE_POINT);

            // Add fields (adjust according to your model)
            $shapefile->addCharField('id');
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
            $shapefile->addCharField('comments');
            $shapefile->addCharField('createdBy');
            $shapefile->addCharField('createdAt');
            $shapefile->addCharField('updatedAt');

            for ($i = 1; $i <= $signsInfoMaxLength; $i++) {
                $shapefile->addCharField('name' . $i);
                $shapefile->addCharField('code' . $i);
                $shapefile->addCharField('codeGcc' . $i);
                $shapefile->addCharField('type' . $i);
                $shapefile->addCharField('shape' . $i);
                $shapefile->addCharField('length' . $i);
                $shapefile->addCharField('width' . $i);
                $shapefile->addCharField('radius' . $i);
                $shapefile->addCharField('color' . $i);
                $shapefile->addCharField('contDes' . $i);
                $shapefile->addCharField('arConte' . $i);
                $shapefile->addCharField('enConte' . $i);
                $shapefile->addCharField('cond' . $i);
            }

            // Add records
            foreach ($groups as $group) {

                $point = new Point($group->longitude, $group->latitude);
                $point->setData('id', $group->id);
                $point->setData('class', $group->road_classification ?? 'N/A');
                $point->setData('rName', $group->road_name ?? 'N/A');
                $point->setData('rNum', $group->road_number ?? 'N/A');
                $point->setData('rType', $group->road_type ?? 'N/A');
                $point->setData('rDir', $group->road_direction ?? 'N/A');
                $point->setData('latitude', $group->latitude ?? 'N/A');
                $point->setData('longitude', $group->longitude ?? 'N/A');
                $point->setData('gov', $group->governorate ?? 'N/A');
                $point->setData('willayat', $group->willayat ?? 'N/A');
                $point->setData('village', $group->village ?? 'N/A');
                $point->setData('signsCount', $group->signs_count ?? 'N/A');
                $point->setData('colDesc', $group->columns_description ?? 'N/A');
                $point->setData('location', $group->sign_location_from_road ?? 'N/A');
                $point->setData('base', $group->sign_base ?? 'N/A');
                $point->setData('distance', $group->distance_from_road_edge_meter ?? 'N/A');
                $point->setData('colRadius', $group->sign_column_radius_mm ?? 'N/A');
                $point->setData('colHeight', $group->column_height ?? 'N/A');
                $point->setData('colColor', $group->column_colour ?? 'N/A');
                $point->setData('colType', $group->column_type ?? 'N/A');
                $point->setData('comments', $group->comments ?? 'N/A');
                $point->setData('createdBy', $group->created_by ?? 'N/A');
                $point->setData('createdAt', $group->created_at ?? 'N/A');
                $point->setData('updatedAt', $group->updated_at ?? 'N/A');

                $groupSignsCount = count($group->signsInfo);

                if ($groupSignsCount > 0) {
                    foreach ($group->signsInfo as $key => $info) {
                        $order = $key + 1;
                        $point->setData('name' . $order, $info->sign_name ?? 'N/A');
                        $point->setData('code' . $order, $info->sign_code ?? 'N/A');
                        $point->setData('codeGcc' . $order, $info->sign_code_gcc ?? 'N/A');
                        $point->setData('type' . $order, $info->sign_type ?? 'N/A');
                        $point->setData('shape' . $order, $info->sign_shape ?? 'N/A');
                        $point->setData('length' . $order, $info->sign_length ?? 'N/A');
                        $point->setData('width' . $order, $info->sign_width ?? 'N/A');
                        $point->setData('radius' . $order, $info->sign_radius ?? 'N/A');
                        $point->setData('color' . $order, $info->sign_color ?? 'N/A');
                        $point->setData('contDes' . $order, $info->sign_content_shape_description ?? 'N/A');
                        $point->setData('arConte' . $order, $info->sign_content_arabic_text ?? 'N/A');
                        $point->setData('enConte' . $order, $info->sign_content_english_text ?? 'N/A');
                        $point->setData('cond' . $order, $info->sign_condition ?? 'N/A');
                    }
                }

                if ($groupSignsCount < $signsInfoMaxLength) {
                    $i = $groupSignsCount;
                    for ($i = $groupSignsCount+1; $i <= $signsInfoMaxLength; $i++) {
                        $point->setData('name' . $i, '--');
                        $point->setData('code' . $i, '--');
                        $point->setData('codeGcc' . $i, '--');
                        $point->setData('type' . $i, '--');
                        $point->setData('shape' . $i, '--');
                        $point->setData('length' . $i, '--');
                        $point->setData('width' . $i, '--');
                        $point->setData('radius' . $i, '--');
                        $point->setData('color' . $i, '--');
                        $point->setData('contDes' . $i, '--');
                        $point->setData('arConte' . $i, '--');
                        $point->setData('enConte' . $i, '--');
                        $point->setData('cond' . $i, '--');
                    }
                }

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
