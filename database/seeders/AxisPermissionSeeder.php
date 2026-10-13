<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class AxisPermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['view', 'create', 'update', 'delete'] as $action) {
            Permission::firstOrCreate(
                ['slug' => "axes.{$action}"],
                [
                    'name' => "Axes {$action}",
                    'module' => 'axes',
                    'description' => "Axes {$action} permission",
                ]
            );
        }
    }
}
