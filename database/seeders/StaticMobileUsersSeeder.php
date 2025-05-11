<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaticMobileUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove any existing users with these emails
        DB::table('users')
            ->whereIn('email', [
                'qais@example.com',
                'thuraiya@example.com',
                'maryam@example.com',
                'naif@example.com',
            ])
            ->delete();

        // Insert the four static mobile users with 'type' => 'Admin'
        DB::table('users')->insert([
            [
                'name'       => 'qais',
                'email'      => 'qais@example.com',
                'password'   => Hash::make('12345678'),
                'type'       => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'thuraiya',
                'email'      => 'thuraiya@example.com',
                'password'   => Hash::make('12345678'),
                'type'       => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'maryam',
                'email'      => 'maryam@example.com',
                'password'   => Hash::make('12345678'),
                'type'       => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'naif',
                'email'      => 'naif@example.com',
                'password'   => Hash::make('12345678'),
                'type'       => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
