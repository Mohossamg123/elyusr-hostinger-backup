<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class NumbersAchievementsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'home_numbers',
            'achievements',
        ];

        $actions = ['view', 'create', 'update', 'delete'];

        $permissions = [];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions[] = Permission::firstOrCreate(
                    [
                        'slug' => "{$module}.{$action}",
                    ],
                    [
                        'module' => $module,
                        'name' => ucfirst(str_replace('_', ' ', $module)) . " {$action}",
                        'description' => "Permission to {$action} {$module}",
                    ]
                );
            }
        }

        $editor = Role::where('slug', 'editor')->first();

        if ($editor) {
            $editor->permissions()->syncWithoutDetaching(
                collect($permissions)->pluck('id')->all()
            );
        }
    }
}