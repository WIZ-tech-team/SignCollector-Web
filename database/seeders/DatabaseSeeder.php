<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // // Roles-Permissions Seed
        // // $this->call(RolesPermissionsSeeder::class);

        // // Users Seed
        // User::whereIn('email', ['super@example.test', 'admin@example.test', 'user@example.test', 'user@example.com'])->forceDelete();
        // // Super Admin
        // User::create([
        //     'name' => 'Super Admin',
        //     'email' => 'super@example.test',
        //     'type' => 'Super-Admin',
        //     'phone' => '+2011123456789',
        //     'password' => Hash::make('password'),
        //     'crypt_password' => Crypt::encryptString('password'),
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ])->assignRole('SuperAdmin');
        // // Viewer
        // User::create([
        //     'name'       => 'Test User',
        //     'email'      => 'user@example.test',
        //     'password'   => Hash::make('12345678'),
        //     'crypt_password'   => Crypt::encryptString('12345678'),
        //     'type'       => 'User',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ])->assignRole('Viewer');

        // // // Places Seed
        // // $this->call(WillayatsGovernoratesSeeder::class);
        // // $this->call(VillagesSeeder::class);
        // // $this->call(RoadsSeeder::class);

        // // Mobile-Web Users Seed
        // $this->call(StaticMobileUsersSeeder::class);

        // // Update roles for mobile users
        // $collectors = User::whereIn('email', [
        //     'qais@example.com',
        //     'thuraiya@example.com',
        //     'maryam@example.com',
        //     'naif@example.com',
        // ])->get();

        // foreach ($collectors as $collector) {
        //     $collector->syncRoles('Collector');
        // }

        // // // Detailed Signs Seed
        // // $this->call(DetailedSignsSeeder::class);

        $users = User::all();
        foreach($users as $user) {
            $user->crypt_password = Crypt::encryptString(Crypt::decrypt($user->crypt_password));
            $user->save();
        }

    }
}
