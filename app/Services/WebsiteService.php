<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Page;
use App\Models\PageRevision;
use App\Models\Template;
use App\Models\Theme;
use App\Models\User;
use App\Models\Website;
use App\Models\WebsiteThemeSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebsiteService
{
    /**
     * Create a new website with optional starter template.
     */
    public function createWebsite(User $user, array $data): Website
    {
        return DB::transaction(function () use ($user, $data) {
            $website = Website::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'subdomain' => Str::slug($data['subdomain']),
                'category' => $data['category'] ?? 'general',
                'description' => $data['description'] ?? null,
                'status' => 'active',
                'is_published' => false,
                'settings' => [
                    'site_name' => $data['name'],
                    'tagline' => 'Built with Antigravity CMS',
                    'contact_email' => $user->email,
                    'language' => 'en',
                    'timezone' => 'UTC',
                ],
            ]);

            // Assign Admin role to creator on this website
            $adminRole = \App\Models\Role::where('slug', 'admin')->first();
            if ($adminRole) {
                $user->roles()->attach($adminRole->id, ['website_id' => $website->id]);
            }

            // Assign default theme
            $theme = Theme::where('slug', 'studio-minimal')->first();
            if ($theme) {
                WebsiteThemeSetting::create([
                    'website_id' => $website->id,
                    'theme_id' => $theme->id,
                    'tokens' => $theme->default_config,
                ]);
            }

            // Determine starter layout from chosen template if provided
            $starterLayout = null;
            if (!empty($data['template_slug'])) {
                $template = Template::where('slug', $data['template_slug'])->first();
                if ($template) {
                    $starterLayout = $template->layout_data;
                }
            }

            // Create initial Home Page
            $homePage = Page::create([
                'website_id' => $website->id,
                'title' => 'Home',
                'slug' => 'home',
                'template' => 'default',
                'status' => 'draft',
                'is_homepage' => true,
                'order_index' => 0,
                'layout_draft' => $starterLayout,
                'layout_published' => null,
            ]);

            if ($starterLayout) {
                PageRevision::create([
                    'page_id' => $homePage->id,
                    'user_id' => $user->id,
                    'revision_number' => 1,
                    'title' => 'Starter Draft Created',
                    'content' => $starterLayout,
                    'is_published_snapshot' => false,
                    'summary' => 'Initial content generated from template: ' . ($data['template_slug'] ?? 'default'),
                    'created_at' => now(),
                ]);
            }

            // Create default Main Menu
            $menu = Menu::create([
                'website_id' => $website->id,
                'name' => 'Main Navigation',
                'slug' => 'main-menu',
                'location' => 'header',
            ]);

            $menu->allItems()->create([
                'title' => 'Home',
                'url' => '/',
                'type' => 'page',
                'reference_id' => $homePage->id,
                'order_index' => 0,
            ]);

            // Create default General Blog Category
            Category::create([
                'website_id' => $website->id,
                'name' => 'General',
                'slug' => 'general',
                'description' => 'General updates and articles.',
            ]);

            ActivityLog::record('website.created', $website->id, $website, [
                'name' => $website->name,
                'subdomain' => $website->subdomain,
            ]);

            return $website->load(['owner', 'homePage', 'themeSettings.theme']);
        });
    }

    /**
     * Duplicate an existing website.
     */
    public function duplicateWebsite(Website $original, User $user, string $newName, string $newSubdomain): Website
    {
        return DB::transaction(function () use ($original, $user, $newName, $newSubdomain) {
            $newWebsite = Website::create([
                'user_id' => $user->id,
                'name' => $newName,
                'subdomain' => Str::slug($newSubdomain),
                'category' => $original->category,
                'description' => $original->description,
                'status' => 'active',
                'is_published' => false,
                'settings' => $original->settings,
                'logo_url' => $original->logo_url,
                'favicon_url' => $original->favicon_url,
            ]);

            // Duplicate Theme Settings
            if ($original->themeSettings) {
                WebsiteThemeSetting::create([
                    'website_id' => $newWebsite->id,
                    'theme_id' => $original->themeSettings->theme_id,
                    'tokens' => $original->themeSettings->tokens,
                    'custom_css' => $original->themeSettings->custom_css,
                    'custom_js' => $original->themeSettings->custom_js,
                ]);
            }

            // Duplicate Pages
            foreach ($original->pages as $page) {
                $newPage = Page::create([
                    'website_id' => $newWebsite->id,
                    'parent_id' => null, // Simplified flat hierarchy copy
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'template' => $page->template,
                    'status' => 'draft',
                    'is_homepage' => $page->is_homepage,
                    'order_index' => $page->order_index,
                    'layout_draft' => $page->layout_draft,
                    'layout_published' => null,
                    'global_styles' => $page->global_styles,
                ]);

                if ($page->layout_draft) {
                    PageRevision::create([
                        'page_id' => $newPage->id,
                        'user_id' => $user->id,
                        'revision_number' => 1,
                        'title' => 'Cloned from ' . $original->name,
                        'content' => $page->layout_draft,
                        'is_published_snapshot' => false,
                        'summary' => 'Duplicated website baseline',
                        'created_at' => now(),
                    ]);
                }
            }

            // Duplicate Categories
            foreach ($original->categories as $category) {
                Category::create([
                    'website_id' => $newWebsite->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                ]);
            }

            // Duplicate Menus
            foreach ($original->menus as $menu) {
                $newMenu = Menu::create([
                    'website_id' => $newWebsite->id,
                    'name' => $menu->name,
                    'slug' => $menu->slug,
                    'location' => $menu->location,
                ]);

                foreach ($menu->allItems as $item) {
                    $newMenu->allItems()->create([
                        'title' => $item->title,
                        'url' => $item->url,
                        'type' => $item->type,
                        'order_index' => $item->order_index,
                    ]);
                }
            }

            ActivityLog::record('website.duplicated', $newWebsite->id, $newWebsite, [
                'original_id' => $original->id,
                'original_name' => $original->name,
            ]);

            return $newWebsite->load(['owner', 'homePage', 'themeSettings.theme']);
        });
    }
}
