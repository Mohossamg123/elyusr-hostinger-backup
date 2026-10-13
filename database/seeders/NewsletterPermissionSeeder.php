<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class NewsletterPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['view', 'create', 'update', 'delete'] as $action) {
            Permission::firstOrCreate(
                ['slug' => "newsletter.{$action}"],
                [
                    'name' => "Newsletter {$action}",
                    'module' => 'newsletter',
                    'description' => "Newsletter {$action} permission",
                ]
            );
        }
    }
}