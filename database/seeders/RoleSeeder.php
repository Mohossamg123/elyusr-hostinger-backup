<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [

            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full access to the system',
            ],

            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Manage website content',
            ],

            [
                'name' => 'Volunteer',
                'slug' => 'volunteer',
                'description' => 'Volunteer in charity activities',
            ],

            [
                'name' => 'Donor',
                'slug' => 'donor',
                'description' => 'Donate to charity projects',
            ],

        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
