<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class ContentApprovalPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'View Content Change Requests',
                'slug' => 'content_change_requests.view',
                'module' => 'content_change_requests',
                'description' => 'View pending content change requests',
            ],
            [
                'name' => 'Approve Content Change Requests',
                'slug' => 'content_change_requests.approve',
                'module' => 'content_change_requests',
                'description' => 'Approve and execute editor changes',
            ],
            [
                'name' => 'Reject Content Change Requests',
                'slug' => 'content_change_requests.reject',
                'module' => 'content_change_requests',
                'description' => 'Reject editor changes',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
