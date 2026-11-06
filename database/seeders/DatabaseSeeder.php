<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Page;
use App\Models\PageRevision;
use App\Models\Post;
use App\Models\Role;
use App\Models\Template;
use App\Models\Theme;
use App\Models\User;
use App\Models\Website;
use App\Models\WebsiteThemeSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles, Permissions, Themes, Templates, Plugins
        $this->call([
            RolesAndPermissionsSeeder::class,
            ThemeAndTemplateSeeder::class,
        ]);

        // 2. Default Super Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@cms.test'],
            [
                'name' => 'Alexander Vance',
                'password' => Hash::make('Password123!'),
                'is_super_admin' => true,
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
            ]
        );

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole && !$admin->roles()->where('role_id', $superAdminRole->id)->exists()) {
            $admin->roles()->attach($superAdminRole->id, ['website_id' => null]);
        }

        // 3. Default Demo Website: Apex Digital Studio
        $website = Website::firstOrCreate(
            ['subdomain' => 'apex'],
            [
                'user_id' => $admin->id,
                'name' => 'Apex Digital Studio',
                'category' => 'agency',
                'description' => 'Modern creative design agency website built with Antigravity CMS.',
                'status' => 'active',
                'is_published' => true,
                'published_at' => now(),
                'settings' => [
                    'site_name' => 'Apex Digital Studio',
                    'tagline' => 'Engineering High Performance Digital Products',
                    'contact_email' => 'hello@apexdigital.test',
                    'primary_color' => '#2563eb',
                    'font_family' => 'Inter, sans-serif',
                ],
            ]
        );

        // Bind theme settings for this website
        $defaultTheme = Theme::where('slug', 'studio-minimal')->first();
        if ($defaultTheme) {
            WebsiteThemeSetting::firstOrCreate(
                ['website_id' => $website->id],
                [
                    'theme_id' => $defaultTheme->id,
                    'tokens' => $defaultTheme->default_config,
                    'custom_css' => "/* Custom Global CSS */\n.hero-gradient { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); }",
                ]
            );
        }

        // Fetch agency template AST layout
        $agencyTemplate = Template::where('slug', 'nexus-agency')->first();
        $starterLayout = $agencyTemplate ? $agencyTemplate->layout_data : null;

        // 4. Default Home Page
        $homePage = Page::firstOrCreate(
            [
                'website_id' => $website->id,
                'slug' => 'home',
            ],
            [
                'title' => 'Home',
                'template' => 'default',
                'status' => 'published',
                'is_homepage' => true,
                'published_at' => now(),
                'order_index' => 0,
                'layout_draft' => $starterLayout,
                'layout_published' => $starterLayout,
            ]
        );

        // Record initial revision
        PageRevision::firstOrCreate(
            [
                'page_id' => $homePage->id,
                'revision_number' => 1,
            ],
            [
                'user_id' => $admin->id,
                'title' => 'Initial Published Revision',
                'content' => $starterLayout,
                'is_published_snapshot' => true,
                'summary' => 'Initial release from Nexus Agency template',
                'created_at' => now(),
            ]
        );

        // 5. Default About and Contact Pages
        Page::firstOrCreate(
            [
                'website_id' => $website->id,
                'slug' => 'about',
            ],
            [
                'title' => 'About Us',
                'template' => 'default',
                'status' => 'published',
                'is_homepage' => false,
                'published_at' => now(),
                'order_index' => 1,
                'layout_draft' => $starterLayout,
                'layout_published' => $starterLayout,
            ]
        );

        Page::firstOrCreate(
            [
                'website_id' => $website->id,
                'slug' => 'contact',
            ],
            [
                'title' => 'Contact',
                'template' => 'default',
                'status' => 'published',
                'is_homepage' => false,
                'published_at' => now(),
                'order_index' => 2,
                'layout_draft' => $starterLayout,
                'layout_published' => $starterLayout,
            ]
        );

        // 6. Default Blog Category & Post
        $category = Category::firstOrCreate(
            [
                'website_id' => $website->id,
                'slug' => 'engineering',
            ],
            [
                'name' => 'Engineering & Design',
                'description' => 'Insights into modern web architecture and visual systems.',
            ]
        );

        $post = Post::firstOrCreate(
            [
                'website_id' => $website->id,
                'slug' => 'launching-the-next-generation-web-experience',
            ],
            [
                'author_id' => $admin->id,
                'title' => 'Launching The Next-Generation Web Experience',
                'excerpt' => 'How we engineered a modular, visual website builder capable of enterprise throughput.',
                'content' => "## The Future of Visual Web Development\n\nModern website building demands both design agility and rigorous software engineering. In this article, we break down our component AST architecture, multi-tenant isolation, and real-time state synchronization.",
                'featured_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80',
                'status' => 'published',
                'published_at' => now(),
                'allow_comments' => true,
                'views_count' => 142,
            ]
        );

        $post->categories()->syncWithoutDetaching([$category->id]);

        // 7. Default Navigation Menu
        $menu = Menu::firstOrCreate(
            [
                'website_id' => $website->id,
                'slug' => 'primary-nav',
            ],
            [
                'name' => 'Main Navigation',
                'location' => 'header',
            ]
        );

        $menu->allItems()->delete();
        $menu->allItems()->createMany([
            ['title' => 'Home', 'url' => '/', 'type' => 'page', 'reference_id' => $homePage->id, 'order_index' => 0],
            ['title' => 'About', 'url' => '/about', 'type' => 'page', 'order_index' => 1],
            ['title' => 'Blog', 'url' => '/blog', 'type' => 'custom', 'order_index' => 2],
            ['title' => 'Contact', 'url' => '/contact', 'type' => 'page', 'order_index' => 3],
        ]);
    }
}
