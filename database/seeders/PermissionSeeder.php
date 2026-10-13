<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'users',
            'roles',
            'permissions',
            'programs',
            'projects',
            'cases',
            'donors',
            'donations',
            'volunteers',
            'reports',
            'settings',
            'payment_methods',
            'stories',
            'content_change_requests',
        ];

        $actions = [
            'view',
            'create',
            'update',
            'delete',
        ];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::updateOrCreate(
                    ['slug' => "$module.$action"],
                    [
                        'name' => ucfirst($action) . " " . ucfirst($module),
                        'slug' => "$module.$action",
                        'module' => $module,
                        'description' => "$action $module",
                    ]
                );
            }
        }
    }
}
