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

class GroupsSignsInfoExport implements FromCollection, WithHeadings, WithMapping
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
            $groups = $query->get()->pluck('id');
        }

        return SignInfo::whereIn('signs_group_id', $groups)->with('group')->get();

    }

    public function map($sign): array
    {
        return [
            $sign->group->id,
            $sign->group->road_classification,
            $sign->group->road_name,
            $sign->group->road_number,
            $sign->group->road_type,
            $sign->group->road_direction,
            $sign->group->latitude,
            $sign->group->longitude,
            $sign->group->governorate,
            $sign->group->willayat,
            $sign->group->village,
            $sign->group->signs_count,
            $sign->group->columns_description,
            $sign->group->sign_location_from_road,
            $sign->group->sign_base,
            $sign->group->distance_from_road_edge_meter,
            $sign->group->sign_column_radius_mm,
            $sign->group->column_height,
            $sign->group->column_colour,
            $sign->group->column_type,
            $sign->group->comments,
            $sign->group->created_by,
            $sign->group->created_at,
            $sign->group->updated_at,
            $sign->sign_name,
            $sign->sign_code,
            $sign->sign_code_gcc,
            $sign->sign_type,
            $sign->sign_shape,
            $sign->sign_length,
            $sign->sign_width,
            $sign->sign_radius,
            $sign->sign_color,
            $sign->sign_content_shape_description,
            $sign->sign_content_arabic_text,
            $sign->sign_content_english_text,
            $sign->sign_condition
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
            'اتجاه الطريق',
            'دائرة عرض',
            'خط طول',
            'المحافظة',
            'الولاية',
            'القرية',
            'عدد الاشارات',
            'وصف الأعمدة',
            'الموقع من الطريق',
            'قاعدة الإشارة',
            'المسافة من حافة الطريق',
            'نصف قطر الإشارة بالميلي',
            'طول العمود',
            'لون العمود',
            'نوع العمود',
            'تعليقات',
            'أنشئ بواسطة',
            'تاريخ الإنشاء',
            'تاريخ التحديث',
            'اسم اللوحة',
            'الرمز (2010)',
            'الرمز (GCC)',
            'نوعية للوحة',
            'الشكل',
            'طول اللوحة (م)',
            'عرض اللوحة (م)',
            'نصف قطر اللوحة (مم)',
            'لون الخلفية',
            '(المحتوى) الشكل المرسوم',
            '(المحتوى) المكتوب بالعربي',
            '(المحتوى) المكتوب بالإنجليزي',
            'حالة اللوحة'
        ];
    }
}
