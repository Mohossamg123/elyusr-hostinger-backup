<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class ContentManagementPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'view',
            'create',
            'update',
            'delete',
        ] as $action) {

            Permission::updateOrCreate(
                [
                    'slug' => "content.$action",
                ],
                [
                    'name' => ucfirst($action) . ' Content',
                    'module' => 'content',
                    'description' => "$action content",
                ]
            );
        }

        $editor = Role::where('slug', 'editor')->first();

        if ($editor) {
            $permissions = Permission::where(
                'module',
                'content'
            )->pluck('id')->all();

            $editor->permissions()
                ->syncWithoutDetaching($permissions);
        }
    }
}
