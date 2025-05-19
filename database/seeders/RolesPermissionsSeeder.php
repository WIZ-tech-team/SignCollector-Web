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
            ['name' => 'access users'],
            ['name' => 'create user'],
            ['name' => 'update user'],
            ['name' => 'delete user'],
            ['name' => 'archive user'],
            ['name' => 'resore user'],
            ['name' => 'show user'],
            ['name' => 'list users'],
            ['name' => 'export users'],

            // Detailed Signs
            ['name' => 'access detailed signs'],
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
            ['name' => 'export auth detailed signs']

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
                'access users',
                'create user',
                'update user',
                'delete user',
                'archive user',
                'resore user',
                'show user',
                'list users',
                'export users',
                // Detailed Signs
                'access detailed signs',
                'update detailed sign',
                'delete detailed sign',
                'export detailed signs',
                'show detailed sign',
                'list detailed signs'
            ]);
        // Super Admin Role
        Role::create(['name' => 'Admin'])
            ->givePermissionTo([
                // Users
                'access users',
                'create user',
                'update user',
                'delete user',
                'archive user',
                'resore user',
                'show user',
                'list users',
                'export users',
                // Detailed Signs
                'access detailed signs',
                'update detailed sign',
                'delete detailed sign',
                'export detailed signs',
                'show detailed sign',
                'list detailed signs'
            ]);
        // Collector Role
        Role::create(['name' => 'Collector'])
            ->givePermissionTo([
                'access detailed signs',
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
                'access detailed signs',
                'show detailed sign',
                'list detailed signs',
                'export detailed signs'
            ]);
    }
}
