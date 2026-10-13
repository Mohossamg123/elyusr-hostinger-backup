<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class BlogSeoPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [];

        foreach (['view', 'create', 'update', 'delete'] as $action) {
            $permissions[] = Permission::firstOrCreate(
                ['slug' => "blog_seo.{$action}"],
                [
                    'name' => "Blog SEO {$action}",
                    'module' => 'blog_seo',
                    'description' => "Permission to {$action} Blog SEO",
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
