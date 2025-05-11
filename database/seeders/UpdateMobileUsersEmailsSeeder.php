<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateMobileUsersEmailsSeeder extends Seeder
{
    public function run(): void
    {
        $emails = [
            'qais@example.com',
            'thuraiya@example.com',
            'maryam@example.com',
            'naif@example.com',
        ];

        // Use split_part to take everything before the '@'
        DB::table('users')
            ->whereIn('email', $emails)
            ->update([
                'email' => DB::raw("split_part(email, '@', 1)")
            ]);
    }
}
