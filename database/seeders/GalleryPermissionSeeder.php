<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class GalleryPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['view', 'create', 'update', 'delete'] as $action) {
            Permission::updateOrCreate(
                ['slug' => "gallery.$action"],
                [
                    'name' => ucfirst($action) . ' Gallery',
                    'module' => 'gallery',
                    'description' => "$action gallery",
                ]
            );
        }

        if ($editor = Role::where('slug', 'editor')->first()) {
            $editor->permissions()->syncWithoutDetaching(
                Permission::where('module', 'gallery')->pluck('id')->all()
            );
        }
    }
}
