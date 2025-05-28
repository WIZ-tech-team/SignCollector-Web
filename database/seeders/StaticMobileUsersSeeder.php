<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
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
        User::whereIn('email', [
            'qais@example.com',
            'thuraiya@example.com',
            'maryam@example.com',
            'naif@example.com',
        ])
            ->forceDelete();

        // Define Users Data
        $usersData = [
            [
                'name'       => 'qais',
                'email'      => 'qais@example.com',
                'password'   => Hash::make('12345678'),
                'crypt_password'   => Crypt::encryptString('12345678'),
                'type'       => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'thuraiya',
                'email'      => 'thuraiya@example.com',
                'password'   => Hash::make('12345678'),
                'crypt_password'   => Crypt::encryptString('12345678'),
                'type'       => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'maryam',
                'email'      => 'maryam@example.com',
                'password'   => Hash::make('12345678'),
                'crypt_password'   => Crypt::encryptString('12345678'),
                'type'       => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'naif',
                'email'      => 'naif@example.com',
                'password'   => Hash::make('12345678'),
                'crypt_password'   => Crypt::encryptString('12345678'),
                'type'       => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert the four static mobile users with 'type' => 'Admin'
        foreach ($usersData as $user) {
            User::create($user)
                ->assignRole('Collector');
        }
    }
}
