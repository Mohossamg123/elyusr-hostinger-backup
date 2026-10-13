<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@alyusr.com'],
            [
                'name' => 'Super Admin',
                'phone' => '01000000000',
                'password' => Hash::make(
                    env('SUPER_ADMIN_PASSWORD', '12345678')
                ),
                'status' => 'active',
            ]
        );

        $role = Role::where('slug', 'super-admin')->first();

        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
