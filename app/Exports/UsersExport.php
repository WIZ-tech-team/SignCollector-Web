<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::query()->get();
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone,
            $user->created_at,
            $user->updated_at
        ];
    }

    public function headings(): array
    {
        return [
            'المعرف',
            'الاسم',
            'الإيميل',
            'رقم الهاتف',
            'وقت الإنشاء',
            'وقت التعديل'
        ];
    }

}
