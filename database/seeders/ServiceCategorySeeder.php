<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_ar' => 'كافة القطاعات',
                'name_en' => 'All Sectors',
                'slug' => 'all-sectors',
                'icon' => 'sparkles',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name_ar' => 'صك الأضحية',
                'name_en' => 'Sacrifice',
                'slug' => 'sacrifice',
                'icon' => 'sacrifice',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الغذاء',
                'name_en' => 'Food',
                'slug' => 'food',
                'icon' => 'utensils',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الصحة',
                'name_en' => 'Health',
                'slug' => 'health',
                'icon' => 'heart-pulse',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name_ar' => 'التكافل الاجتماعي',
                'name_en' => 'Social Solidarity',
                'slug' => 'social-solidarity',
                'icon' => 'users',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الإسكان والمنشآت',
                'name_en' => 'Housing & Facilities',
                'slug' => 'housing-facilities',
                'icon' => 'house',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
