<?php

namespace App\Exports;

use App\Models\Governorate;
use App\Models\Road;
use App\Models\SignInfo;
use App\Models\SignsGroup;
use App\Models\Willayat;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Symfony\Component\HttpFoundation\Response;

class SignsGroupsExport implements FromCollection, WithHeadings, WithMapping
{

    private Governorate|null $governorate;
    private Willayat|null $willayat;
    private Road|null $road;

    public function __construct($g, $w, $r)
    {
        $this->governorate = $g;
        $this->willayat = $w;
        $this->road = $r;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
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

        $groups = null;

        if ($this->governorate) {
            if ($this->willayat) {
                $groups = $query->where('governorate', $this->governorate->name_ar)
                    ->where('willayat', $this->willayat->name_ar)
                    ->get()->pluck('id');
            } else {
                $groups = $query->where('governorate', $this->governorate->name_ar)->get()->pluck('id');
            }
        } elseif ($this->road) {
            $groups = $query->where('road_name', $this->road->name)->get()->pluck('id');
        } else {
            $groups = $query->get();
        }

        return $groups;

    }

    public function map($group): array
    {
        return [
            $group->id,
            $group->road_classification,
            $group->road_name,
            $group->road_number,
            $group->road_type,
            $group->road_direction,
            $group->latitude,
            $group->longitude,
            $group->governorate,
            $group->willayat,
            $group->village,
            $group->signs_count,
            $group->columns_description,
            $group->sign_location_from_road,
            $group->sign_base,
            $group->distance_from_road_edge_meter,
            $group->sign_column_radius_mm,
            $group->column_height,
            $group->column_colour,
            $group->column_type,
            $group->comments,
            $group->created_by,
            $group->created_at,
            $group->updated_at
        ];
    }

    public function headings(): array
    {
        return [
            'المعرف',
            'تصنيف الطريق',
            'اسم الطريق',
            'رقم الطريق',
            'نوع الطريق',
            'الطريق نحو',
            'دائرة العرض',
            'خط الطول',
            'المحافظة',
            'الولاية',
            'القرية',
            'عدد (تسلسل) اللوحات',
            'وصف الأعمدة',
            'موقع اللوحة من الطريق',
            'قاعدة الإشارة',
            'المسافة من نهاية كتف  الطريق حتى اللوحة (م)',
            'نصف قطر أنبوب اللوحة (مم)',
            'طول العمود (م)',
            'لون العمود',
            'نوع الأعمدة',
            'ملاحظات أُخرى',
            'مدخل البيانات',
            'تاريخ إدخال البيانات',
            'تاريخ تحديث البيانات'
        ];
    }
}
