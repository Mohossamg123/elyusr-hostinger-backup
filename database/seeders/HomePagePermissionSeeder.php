<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class HomePagePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [];

        foreach (['view','update'] as $action) {
            $permissions[] = Permission::firstOrCreate(
                ['slug' => "homepage.{$action}"],
                [
                    'name' => "Homepage {$action}",
                    'module' => 'homepage',
                    'description' => "Permission to {$action} homepage content",
                ]
            );
        }

        $editor = Role::where('slug', 'editor')->first();

        if ($editor) {
            $editor->permissions()->syncWithoutDetaching(
                collect($permissions)->pluck('id')->all()
            );
        }
    }
}
