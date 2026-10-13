<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

 

public function run(): void
{
    $this->call([
    PaymentGatewaySettingSeeder::class,
    PaymentMethodSeeder::class,
    ServiceCategorySeeder::class,

    RoleSeeder::class,
    PermissionSeeder::class,

    AxisPermissionSeeder::class,
    BlogSeoPermissionSeeder::class,
    ContentManagementPermissionSeeder::class,
    ContentApprovalPermissionSeeder::class,
    GalleryPermissionSeeder::class,
    HomePagePermissionSeeder::class,
    NewsletterPermissionSeeder::class,
    NumbersAchievementsPermissionSeeder::class,
    VolunteerCampaignPermissionSeeder::class,

    EditorPermissionSeeder::class,

    SuperAdminSeeder::class,
]);
}
}