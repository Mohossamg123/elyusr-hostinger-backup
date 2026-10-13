<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class VolunteerCampaignPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'volunteers',
            'volunteer_campaigns',
            'volunteer_applications',
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
                    ['slug' => "{$module}.{$action}"],
                    [
                        'name' => ucfirst($action) . ' ' . ucfirst(str_replace('_', ' ', $module)),
                        'slug' => "{$module}.{$action}",
                        'module' => $module,
                        'description' => "{$action} {$module}",
                    ]
                );
            }
        }
    }
}
