<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Define Basic Permissions
        $permissionsData = [
            // Users
            ['name' => 'create user'],
            ['name' => 'update user'],
            ['name' => 'delete user'],
            ['name' => 'archive user'],
            ['name' => 'resore user'],
            ['name' => 'show user'],
            ['name' => 'list users'],
            ['name' => 'export users'],

            // Detailed Signs
            ['name' => 'update detailed sign'],
            ['name' => 'delete detailed sign'],
            ['name' => 'export detailed signs'],
            ['name' => 'show detailed sign'],
            ['name' => 'list detailed signs'],

            // Special for Auth User
            ['name' => 'list auth detailed signs'],
            ['name' => 'show auth detailed sign'],
            ['name' => 'update auth detailed sign'],
            ['name' => 'delete auth detailed sign'],
            ['name' => 'export auth detailed signs'],

            // Special for All Data
            ['name' => 'list all detailed signs'],
            ['name' => 'show all detailed sign'],
            ['name' => 'update all detailed sign'],
            ['name' => 'delete all detailed sign'],
            ['name' => 'export all detailed signs']

        ];

        // Seed Permissions
        Permission::truncate();
        foreach ($permissionsData as $permisssion) {
            Permission::create($permisssion);
        }

        // Seed Basic Roles
        Role::truncate();
        // Super Admin Role
        Role::create(['name' => 'SuperAdmin'])
            ->givePermissionTo([
                // Users
                'create user',
                'update user',
                'delete user',
                'archive user',
                'resore user',
                'show user',
                'list users',
                'export users',
                // Detailed Signs
                'update detailed sign',
                'delete detailed sign',
                'export detailed signs',
                'show detailed sign',
                'list detailed signs',
                // Special for All Data
                'list all detailed signs',
                'show all detailed sign',
                'update all detailed sign',
                'delete all detailed sign',
                'export all detailed signs'
            ]);
        // Collector Role
        Role::create(['name' => 'Collector'])
            ->givePermissionTo([
                'update detailed sign',
                'delete detailed sign',
                'export detailed signs',
                'show detailed sign',
                'list detailed signs',
                'list auth detailed signs',
                'show auth detailed sign',
                'update auth detailed sign',
                'delete auth detailed sign',
                'export auth detailed signs'
            ]);
        // Viewer Role
        Role::create(['name' => 'Viewer'])
            ->givePermissionTo([
                'show detailed sign',
                'list detailed signs',
                'list all detailed signs',
                'show all detailed sign',
                'export all detailed signs'
            ]);
    }
}
