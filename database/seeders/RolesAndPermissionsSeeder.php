<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Manage Websites', 'slug' => 'manage-websites', 'category' => 'websites', 'description' => 'Create, edit, duplicate, and delete websites'],
            ['name' => 'Manage Pages', 'slug' => 'manage-pages', 'category' => 'pages', 'description' => 'Create, delete, and configure pages and routing'],
            ['name' => 'Edit Pages', 'slug' => 'edit-pages', 'category' => 'pages', 'description' => 'Edit page visual content in builder'],
            ['name' => 'Publish Pages', 'slug' => 'publish-pages', 'category' => 'pages', 'description' => 'Publish and roll back page revisions'],
            ['name' => 'Manage Blog Posts', 'slug' => 'manage-posts', 'category' => 'blog', 'description' => 'Create and edit blog posts, categories, and tags'],
            ['name' => 'Manage Media', 'slug' => 'manage-media', 'category' => 'media', 'description' => 'Upload, organize, and delete files in media library'],
            ['name' => 'Manage Menus', 'slug' => 'manage-menus', 'category' => 'navigation', 'description' => 'Configure navigation menus and hierarchy'],
            ['name' => 'Manage Themes', 'slug' => 'manage-themes', 'category' => 'design', 'description' => 'Customize theme tokens, colors, typography, and CSS'],
            ['name' => 'Manage Plugins', 'slug' => 'manage-plugins', 'category' => 'plugins', 'description' => 'Enable, configure, and install plugins'],
            ['name' => 'Manage Settings', 'slug' => 'manage-settings', 'category' => 'settings', 'description' => 'Configure domains, SEO, and website settings'],
            ['name' => 'Manage Users & Roles', 'slug' => 'manage-users', 'category' => 'users', 'description' => 'Invite users, assign roles, and modify permissions'],
            ['name' => 'View Analytics', 'slug' => 'view-analytics', 'category' => 'analytics', 'description' => 'View traffic, pageviews, and conversion telemetry'],
        ];

        $createdPermissions = [];
        foreach ($permissions as $perm) {
            $createdPermissions[$perm['slug']] = Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }

        $roles = [
            [
                'name' => 'Super Administrator',
                'slug' => 'super-admin',
                'description' => 'Full administrative access across all websites and system infrastructure.',
                'permissions' => array_keys($createdPermissions),
            ],
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Complete control over a specific website and its content, settings, and users.',
                'permissions' => array_keys($createdPermissions),
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Can create, edit, and publish pages, posts, media, and navigation menus.',
                'permissions' => ['manage-pages', 'edit-pages', 'publish-pages', 'manage-posts', 'manage-media', 'manage-menus', 'view-analytics'],
            ],
            [
                'name' => 'Author',
                'slug' => 'author',
                'description' => 'Can write, edit, and publish their own posts and manage their own media.',
                'permissions' => ['manage-posts', 'manage-media'],
            ],
            [
                'name' => 'Contributor',
                'slug' => 'contributor',
                'description' => 'Can write drafts of posts and pages, but cannot publish.',
                'permissions' => ['edit-pages', 'manage-posts'],
            ],
            [
                'name' => 'Subscriber',
                'slug' => 'subscriber',
                'description' => 'Can read restricted content and submit comments or forms.',
                'permissions' => [],
            ],
        ];

        foreach ($roles as $roleData) {
            $permSlugs = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
            
            $permIds = [];
            foreach ($permSlugs as $slug) {
                if (isset($createdPermissions[$slug])) {
                    $permIds[] = $createdPermissions[$slug]->id;
                }
            }
            $role->permissions()->sync($permIds);
        }
    }
}
