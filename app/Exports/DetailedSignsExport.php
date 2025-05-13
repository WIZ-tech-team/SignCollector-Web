<?php

namespace App\Exports;

use App\Models\DetailedSign;
use App\Models\Governorate;
use App\Models\Road;
use App\Models\Willayat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DetailedSignsExport implements FromCollection, WithHeadings
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
        if ($this->governorate) {
            if ($this->willayat) {
                return DetailedSign::where('governorate', $this->governorate->name_ar)
                    ->where('willayat', $this->willayat->name_ar)
                    ->get();
            } else {
                return DetailedSign::where('governorate', $this->governorate->name_ar)->get();
            }
        } elseif ($this->road) {
            return DetailedSign::where('road_name', $this->road->name)->get();
        } else {
            return DetailedSign::all();
        }
    }

    public function map($sign): array
    {
        return [
            $sign->id,
            $sign->sign_name,
            $sign->sign_code,
            $sign->sign_code_gcc,
            $sign->sign_type,
            $sign->sign_shape,
            $sign->sign_length,
            $sign->sign_width,
            $sign->sign_radius,
            $sign->sign_color,
            $sign->road_classification,
            $sign->road_name,
            $sign->road_number,
            $sign->road_type,
            $sign->road_direction,
            $sign->latitude,
            $sign->longitude,
            $sign->governorate,
            $sign->willayat,
            $sign->village,
            $sign->signs_count,
            $sign->columns_description,
            $sign->sign_location_from_road,
            $sign->sign_base,
            $sign->distance_from_road_edge_meter,
            $sign->sign_column_radius_mm,
            $sign->column_height,
            $sign->column_colour,
            $sign->column_type,
            $sign->sign_content_shape_description,
            $sign->sign_content_arabic_text,
            $sign->sign_content_english_text,
            $sign->sign_condition,
            $sign->comments,
            $sign->created_by,
            $sign->created_at,
            $sign->updated_at
        ];
    }

    public function headings(): array
    {
        return [
            'المعرف',
            'اسم الإشارة',
            'كود الإشارة',
            'كود الإشارة',
            'نوع الإشارة',
            'شكل الإشارة',
            'طول الإشارة',
            'عرض الإشارة',
            'نصف قطر الإشارة',
            'لون الإشارة',
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
            'وصف شكل محتوى الإشارة',
            'وصف الأشارة بالعربية',
            'وصف الإشارة بالإنجليزية',
            'حالة الإشارة',
            'تعليقات',
            'أنشئ بواسطة',
            'تاريخ الإنشاء',
            'تاريخ التحديث'
        ];
    }
}
