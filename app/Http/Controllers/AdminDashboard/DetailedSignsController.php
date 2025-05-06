<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Exports\DetailedSignsExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedSignResource;
use App\Models\DetailedSign;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class DetailedSignsController extends Controller
{
    public function index()
    {
        $signs = DetailedSign::with('image')->paginate(DetailedSign::count());

        return response()->json([
            'status' => 'success',
            'data' => $signs
        ], Response::HTTP_OK);
    }

    public function export()
    {
        return Excel::download(
            new DetailedSignsExport(),
            'Signs_' . today()->format('Y-m-d') . '.xlsx', // Ensure extension here
            ExcelExcel::XLSX
        );
    }
}
