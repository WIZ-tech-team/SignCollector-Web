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
        // User::factory(10)->create();
        $this->call(WillayatsGovernoratesSeeder::class);
        $this->call(VillagesSeeder::class);
        $this->call(RoadsSeeder::class);
        // $this->call(StaticMobileUsersSeeder::class);
        // $this->call(DetailedSignsTableSeeder::class);

        // User::factory()->create([
        //     'name' => 'Test Admin',
        //     'email' => 'admin@example.test',
        //     'type' => 'Admin',
        //     'phone' => '+2011123456789',
        //     'password' => Hash::make('password')
        // ]);

    }
}
