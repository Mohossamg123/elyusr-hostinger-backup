<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class EditorPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $editor = Role::where('slug', 'editor')->first();

        if (!$editor) {
            return;
        }

        /*
         * Editor can manage website/content modules.
         *
         * Sensitive system-management permissions such as:
         * users, roles, permissions and payment gateway settings
         * are intentionally excluded.
         */
        $editorModules = [
            'programs',
            'projects',
            'cases',
            'case_categories',
            'services',
            'service_categories',
            'donation_campaigns',
            'donation_campaign_categories',
            'donation_packages',
            'faqs',
            'faq_categories',
            'donors',
            'volunteers',
            'volunteer_campaigns',
            'stories',
            'content',
            'gallery',
            'home_numbers',
            'achievements',
            'blog_seo',
            'newsletter',
            'homepage',
            'about_us',
            'axes',
            'seasons',
            'settings',
        ];

        $permissions = Permission::query()
            ->whereIn('module', $editorModules)
            ->pluck('id')
            ->toArray();

        $editor->permissions()->syncWithoutDetaching($permissions);
    }
}