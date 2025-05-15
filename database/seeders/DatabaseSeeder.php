<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Roles-Permissions Seed
        $this->call(RolesPermissionsSeeder::class);

        // Users Seed
        User::truncate();
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.test',
            'type' => 'Super-Admin',
            'phone' => '+2011123456789',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ])->assignRole('SuperAdmin');
        // Viewer
        User::create([
            'name'       => 'Test User',
            'email'      => 'user@example.com',
            'password'   => Hash::make('12345678'),
            'type'       => 'User',
            'created_at' => now(),
            'updated_at' => now(),
        ])->assignRole('Viewer');

        // Places Seed
        $this->call(WillayatsGovernoratesSeeder::class);
        $this->call(VillagesSeeder::class);
        $this->call(RoadsSeeder::class);

        // Mobile-Web Users Seed
        $this->call(StaticMobileUsersSeeder::class);

        // Detailed Signs Seed
        $this->call(DetailedSignsTableSeeder::class);
    }
}
