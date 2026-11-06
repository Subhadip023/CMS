<?php

namespace Database\Seeders;

use App\Models\Plugin;
use App\Models\Template;
use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeAndTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Themes
        $themes = [
            [
                'name' => 'Studio Minimal',
                'slug' => 'studio-minimal',
                'description' => 'Clean, typography-focused modern minimalist theme designed for high conversion.',
                'version' => '1.0.0',
                'preview_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80',
                'default_config' => [
                    'primary_color' => '#0f172a',
                    'accent_color' => '#3b82f6',
                    'background_color' => '#ffffff',
                    'surface_color' => '#f8fafc',
                    'text_color' => '#1e293b',
                    'font_family_heading' => 'Inter, sans-serif',
                    'font_family_body' => 'Inter, sans-serif',
                    'border_radius' => '0.5rem',
                ],
            ],
            [
                'name' => 'Cyber Dark Pro',
                'slug' => 'cyber-dark-pro',
                'description' => 'Sleek dark mode theme with neon accents for tech startups and digital products.',
                'version' => '1.0.0',
                'preview_image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80',
                'default_config' => [
                    'primary_color' => '#6366f1',
                    'accent_color' => '#10b981',
                    'background_color' => '#090d16',
                    'surface_color' => '#111827',
                    'text_color' => '#f3f4f6',
                    'font_family_heading' => 'Plus Jakarta Sans, sans-serif',
                    'font_family_body' => 'Plus Jakarta Sans, sans-serif',
                    'border_radius' => '0.75rem',
                ],
            ],
            [
                'name' => 'Editorial Serif',
                'slug' => 'editorial-serif',
                'description' => 'Refined, sophisticated editorial theme featuring classic serif headings and warm tones.',
                'version' => '1.0.0',
                'preview_image' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=600&q=80',
                'default_config' => [
                    'primary_color' => '#78350f',
                    'accent_color' => '#d97706',
                    'background_color' => '#fefaf6',
                    'surface_color' => '#fffbeb',
                    'text_color' => '#292524',
                    'font_family_heading' => 'Playfair Display, serif',
                    'font_family_body' => 'Inter, sans-serif',
                    'border_radius' => '0.25rem',
                ],
            ],
        ];

        foreach ($themes as $themeData) {
            Theme::firstOrCreate(['slug' => $themeData['slug']], $themeData);
        }

        // Helper to generate starter AST
        $createHeroAst = function ($headline, $subheadline, $ctaText, $categoryName) {
            return [
                'id' => 'root',
                'type' => 'container',
                'name' => 'Page Container',
                'props' => [],
                'styles' => ['desktop' => ['display' => 'flex', 'flexDirection' => 'column', 'minHeight' => '100vh']],
                'children' => [
                    // Navbar
                    [
                        'id' => 'node_nav_1',
                        'type' => 'navbar',
                        'name' => 'Site Navigation',
                        'props' => [
                            'brandName' => $categoryName,
                            'links' => [
                                ['label' => 'Home', 'url' => '#'],
                                ['label' => 'About', 'url' => '#about'],
                                ['label' => 'Services', 'url' => '#services'],
                                ['label' => 'Pricing', 'url' => '#pricing'],
                                ['label' => 'Contact', 'url' => '#contact'],
                            ],
                            'ctaText' => 'Get Started',
                            'ctaUrl' => '#contact',
                        ],
                        'styles' => [
                            'desktop' => [
                                'padding' => ['top' => '1rem', 'bottom' => '1rem', 'left' => '2rem', 'right' => '2rem'],
                                'backgroundColor' => '#ffffff',
                                'borderWidth' => '0 0 1px 0',
                                'borderColor' => '#e2e8f0',
                            ],
                        ],
                        'children' => [],
                    ],
                    // Hero Section
                    [
                        'id' => 'node_hero_sec',
                        'type' => 'section',
                        'name' => 'Hero Section',
                        'props' => [],
                        'styles' => [
                            'desktop' => [
                                'padding' => ['top' => '6rem', 'bottom' => '6rem', 'left' => '2rem', 'right' => '2rem'],
                                'backgroundColor' => '#f8fafc',
                                'textAlign' => 'center',
                            ],
                        ],
                        'children' => [
                            [
                                'id' => 'node_badge',
                                'type' => 'badge',
                                'name' => 'Category Pill',
                                'props' => ['text' => '✨ Next-Gen ' . $categoryName . ' Experience'],
                                'styles' => [
                                    'desktop' => [
                                        'color' => '#2563eb',
                                        'backgroundColor' => '#dbeafe',
                                        'padding' => ['top' => '0.25rem', 'bottom' => '0.25rem', 'left' => '0.75rem', 'right' => '0.75rem'],
                                        'borderRadius' => ['topLeft' => '9999px', 'topRight' => '9999px', 'bottomRight' => '9999px', 'bottomLeft' => '9999px'],
                                        'margin' => ['bottom' => '1.5rem'],
                                        'display' => 'inline-block',
                                    ],
                                ],
                                'children' => [],
                            ],
                            [
                                'id' => 'node_h1',
                                'type' => 'heading',
                                'name' => 'Hero Title',
                                'props' => ['text' => $headline, 'level' => 1],
                                'styles' => [
                                    'desktop' => [
                                        'fontSize' => '3.5rem',
                                        'fontWeight' => '800',
                                        'lineHeight' => '1.15',
                                        'color' => '#0f172a',
                                        'margin' => ['bottom' => '1.25rem'],
                                        'maxWidth' => '900px',
                                    ],
                                ],
                                'children' => [],
                            ],
                            [
                                'id' => 'node_p1',
                                'type' => 'paragraph',
                                'name' => 'Hero Subtitle',
                                'props' => ['text' => $subheadline],
                                'styles' => [
                                    'desktop' => [
                                        'fontSize' => '1.25rem',
                                        'color' => '#475569',
                                        'lineHeight' => '1.6',
                                        'maxWidth' => '700px',
                                        'margin' => ['bottom' => '2.5rem'],
                                    ],
                                ],
                                'children' => [],
                            ],
                            [
                                'id' => 'node_cta_btn',
                                'type' => 'button',
                                'name' => 'Hero Primary Button',
                                'props' => ['text' => $ctaText, 'url' => '#contact', 'variant' => 'primary'],
                                'styles' => [
                                    'desktop' => [
                                        'backgroundColor' => '#2563eb',
                                        'color' => '#ffffff',
                                        'padding' => ['top' => '0.875rem', 'bottom' => '0.875rem', 'left' => '2rem', 'right' => '2rem'],
                                        'borderRadius' => ['topLeft' => '0.5rem', 'topRight' => '0.5rem', 'bottomRight' => '0.5rem', 'bottomLeft' => '0.5rem'],
                                        'fontWeight' => '600',
                                        'fontSize' => '1.125rem',
                                    ],
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                    // Features Grid
                    [
                        'id' => 'node_features_sec',
                        'type' => 'section',
                        'name' => 'Features Section',
                        'props' => [],
                        'styles' => [
                            'desktop' => [
                                'padding' => ['top' => '5rem', 'bottom' => '5rem', 'left' => '2rem', 'right' => '2rem'],
                                'backgroundColor' => '#ffffff',
                            ],
                        ],
                        'children' => [
                            [
                                'id' => 'node_feat_grid',
                                'type' => 'grid',
                                'name' => 'Features Grid',
                                'props' => ['columns' => 3],
                                'styles' => [
                                    'desktop' => [
                                        'display' => 'grid',
                                        'gridColumns' => 'repeat(3, minmax(0, 1fr))',
                                        'gap' => '2rem',
                                        'maxWidth' => '1200px',
                                    ],
                                ],
                                'children' => [
                                    [
                                        'id' => 'node_feat_card_1',
                                        'type' => 'card',
                                        'name' => 'Feature Card 1',
                                        'props' => [
                                            'title' => 'High Speed Performance',
                                            'description' => 'Engineered with cutting-edge asset caching and lightning fast server response times.',
                                            'icon' => 'Zap',
                                        ],
                                        'styles' => [
                                            'desktop' => [
                                                'padding' => ['top' => '2rem', 'bottom' => '2rem', 'left' => '1.5rem', 'right' => '1.5rem'],
                                                'backgroundColor' => '#f8fafc',
                                                'borderRadius' => ['topLeft' => '0.75rem', 'topRight' => '0.75rem', 'bottomRight' => '0.75rem', 'bottomLeft' => '0.75rem'],
                                            ],
                                        ],
                                        'children' => [],
                                    ],
                                    [
                                        'id' => 'node_feat_card_2',
                                        'type' => 'card',
                                        'name' => 'Feature Card 2',
                                        'props' => [
                                            'title' => 'Modern Architecture',
                                            'description' => 'Modular component building blocks and clean design token systems for seamless scaling.',
                                            'icon' => 'Shield',
                                        ],
                                        'styles' => [
                                            'desktop' => [
                                                'padding' => ['top' => '2rem', 'bottom' => '2rem', 'left' => '1.5rem', 'right' => '1.5rem'],
                                                'backgroundColor' => '#f8fafc',
                                                'borderRadius' => ['topLeft' => '0.75rem', 'topRight' => '0.75rem', 'bottomRight' => '0.75rem', 'bottomLeft' => '0.75rem'],
                                            ],
                                        ],
                                        'children' => [],
                                    ],
                                    [
                                        'id' => 'node_feat_card_3',
                                        'type' => 'card',
                                        'name' => 'Feature Card 3',
                                        'props' => [
                                            'title' => 'Seamless Integrations',
                                            'description' => 'Connect effortlessly with modern APIs, marketing suites, and analytics providers.',
                                            'icon' => 'Globe',
                                        ],
                                        'styles' => [
                                            'desktop' => [
                                                'padding' => ['top' => '2rem', 'bottom' => '2rem', 'left' => '1.5rem', 'right' => '1.5rem'],
                                                'backgroundColor' => '#f8fafc',
                                                'borderRadius' => ['topLeft' => '0.75rem', 'topRight' => '0.75rem', 'bottomRight' => '0.75rem', 'bottomLeft' => '0.75rem'],
                                            ],
                                        ],
                                        'children' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    // Footer
                    [
                        'id' => 'node_footer_1',
                        'type' => 'footer',
                        'name' => 'Global Footer',
                        'props' => [
                            'copyright' => '© ' . date('Y') . ' ' . $categoryName . '. All rights reserved.',
                            'socialLinks' => [
                                ['platform' => 'Twitter', 'url' => 'https://twitter.com'],
                                ['platform' => 'GitHub', 'url' => 'https://github.com'],
                                ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com'],
                            ],
                        ],
                        'styles' => [
                            'desktop' => [
                                'padding' => ['top' => '3rem', 'bottom' => '3rem', 'left' => '2rem', 'right' => '2rem'],
                                'backgroundColor' => '#0f172a',
                                'color' => '#94a3b8',
                                'textAlign' => 'center',
                            ],
                        ],
                        'children' => [],
                    ],
                ],
            ];
        };

        // 2. Templates (All 9 required categories)
        $templates = [
            [
                'name' => 'Apex Business Suite',
                'slug' => 'apex-business',
                'category' => 'business',
                'description' => 'Sophisticated corporate and enterprise website template designed for trust and authority.',
                'thumbnail' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80',
                'layout_data' => $createHeroAst(
                    'Drive Exponential Enterprise Growth With Precision',
                    'Deliver world-class digital experiences and transform your market presence with our proven consulting framework.',
                    'Schedule Consultation',
                    'Apex Business'
                ),
            ],
            [
                'name' => 'Aura Creative Portfolio',
                'slug' => 'aura-portfolio',
                'category' => 'portfolio',
                'description' => 'Visual-first portfolio showcasing design, photography, and creative client projects.',
                'thumbnail' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=600&q=80',
                'layout_data' => $createHeroAst(
                    'Crafting Visual Masterpieces & Brand Identities',
                    'Selected works across digital product design, art direction, and interactive web experiences.',
                    'View Selected Works',
                    'Aura Portfolio'
                ),
            ],
            [
                'name' => 'Nexus Digital Agency',
                'slug' => 'nexus-agency',
                'category' => 'agency',
                'description' => 'Dynamic full-service agency template highlighting client case studies and services.',
                'thumbnail' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=600&q=80',
                'layout_data' => $createHeroAst(
                    'We Engineer Scalable Digital Products & Brands',
                    'A collective of elite engineers, designers, and strategists building the future of web and mobile software.',
                    'Start a Project',
                    'Nexus Agency'
                ),
            ],
            [
                'name' => 'Vogue E-Commerce Store',
                'slug' => 'vogue-ecommerce',
                'category' => 'ecommerce',
                'description' => 'Clean e-commerce showcase template with product grids, hero banners, and promos.',
                'thumbnail' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?auto=format&fit=crop&w=600&q=80',
                'layout_data' => $createHeroAst(
                    'Curated Sustainable Luxury Goods For Everyday Living',
                    'Explore our new seasonal collection featuring handcrafted apparel, timeless accessories, and home goods.',
                    'Shop Collection',
                    'Vogue Store'
                ),
            ],
            [
                'name' => 'Chronicle Journal & Blog',
                'slug' => 'chronicle-blog',
                'category' => 'blog',
                'description' => 'Reader-optimized editorial publication and thought leadership blog template.',
                'thumbnail' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=600&q=80',
                'layout_data' => $createHeroAst(
                    'Deep Dives Into Technology, Culture & Modern Design',
                    'Articles, essays, and critical analysis written by industry leaders and software craftsmen.',
                    'Read Latest Stories',
                    'Chronicle'
                ),
            ],
            [
                'name' => 'Vanguard Corporate Group',
                'slug' => 'vanguard-corporate',
                'category' => 'corporate',
                'description' => 'Comprehensive multi-division corporate website for institutions and organizations.',
                'thumbnail' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80',
                'layout_data' => $createHeroAst(
                    'Institutional Investment & Global Strategic Partnerships',
                    'Delivering enduring value to global stakeholders through disciplined capital allocation and ethical governance.',
                    'Investor Relations',
                    'Vanguard Group'
                ),
            ],
            [
                'name' => 'Pulse SaaS Landing Page',
                'slug' => 'pulse-landing',
                'category' => 'landing',
                'description' => 'High-conversion SaaS product landing page with interactive pricing and testimonials.',
                'thumbnail' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80',
                'layout_data' => $createHeroAst(
                    'Turn Your Data Into Instant Revenue Insights',
                    'The all-in-one telemetry and analytics workspace designed for fast-moving product and growth teams.',
                    'Start 14-Day Free Trial',
                    'Pulse Analytics'
                ),
            ],
            [
                'name' => 'Devon Personal Brand',
                'slug' => 'devon-personal',
                'category' => 'personal',
                'description' => 'Personal website and CV template for creators, speakers, and executives.',
                'thumbnail' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
                'layout_data' => $createHeroAst(
                    'Keynote Speaker, Author & Technology Strategist',
                    'Helping fortune 500 executives navigate artificial intelligence and modern organizational change.',
                    'Book For Speaking',
                    'Devon Vance'
                ),
            ],
            [
                'name' => 'Athena Learning Academy',
                'slug' => 'athena-education',
                'category' => 'education',
                'description' => 'Curriculum-focused educational academy and course directory template.',
                'thumbnail' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80',
                'layout_data' => $createHeroAst(
                    'Master In-Demand Modern Engineering & AI Skills',
                    'Accredited, project-based online cohorts mentored by world-class senior industry practitioners.',
                    'Explore Courses',
                    'Athena Academy'
                ),
            ],
        ];

        foreach ($templates as $tmpl) {
            Template::firstOrCreate(['slug' => $tmpl['slug']], $tmpl);
        }

        // 3. Plugins
        $plugins = [
            [
                'name' => 'Form Builder Pro',
                'slug' => 'form-builder-pro',
                'version' => '1.0.0',
                'description' => 'Captures lead inquiries, surveys, and contact forms with automated email notifications and CSV export.',
                'author' => 'Antigravity Core',
                'settings_schema' => [
                    'recipient_email' => ['type' => 'string', 'label' => 'Notification Email Address', 'default' => 'admin@example.com'],
                    'enable_recaptcha' => ['type' => 'boolean', 'label' => 'Spam Honeypot Protection', 'default' => true],
                    'success_message' => ['type' => 'string', 'label' => 'Success Confirmation Message', 'default' => 'Thank you! Your message has been received.'],
                ],
                'hooks_manifest' => ['on_form_submit', 'render_form_widget'],
                'is_system' => true,
            ],
            [
                'name' => 'Advanced SEO & Schema',
                'slug' => 'seo-master',
                'version' => '1.0.0',
                'description' => 'Auto-generates sitemap.xml, robots.txt, Open Graph meta tags, and rich Google Schema.org structured data.',
                'author' => 'Antigravity Core',
                'settings_schema' => [
                    'auto_sitemap' => ['type' => 'boolean', 'label' => 'Automatic Sitemap Ping', 'default' => true],
                    'default_og_image' => ['type' => 'string', 'label' => 'Fallback Social Sharing Image URL', 'default' => ''],
                    'schema_type' => ['type' => 'select', 'options' => ['Organization', 'LocalBusiness', 'Person'], 'default' => 'Organization'],
                ],
                'hooks_manifest' => ['head_meta_tags', 'generate_sitemap'],
                'is_system' => true,
            ],
            [
                'name' => 'Google Analytics & Telemetry',
                'slug' => 'google-analytics',
                'version' => '1.0.0',
                'description' => 'Integrated tracking with Google Analytics 4 (GA4) or Google Tag Manager without writing code.',
                'author' => 'Antigravity Core',
                'settings_schema' => [
                    'measurement_id' => ['type' => 'string', 'label' => 'GA4 Measurement ID (G-XXXXXXXXXX)', 'default' => ''],
                    'anonymize_ip' => ['type' => 'boolean', 'label' => 'GDPR IP Anonymization', 'default' => true],
                ],
                'hooks_manifest' => ['head_scripts', 'body_scripts'],
                'is_system' => true,
            ],
            [
                'name' => 'Custom CSS & Head Injector',
                'slug' => 'custom-code-injector',
                'version' => '1.0.0',
                'description' => 'Safely inject global custom stylesheet rules and verification meta tags into the public site header.',
                'author' => 'Antigravity Core',
                'settings_schema' => [
                    'custom_header_code' => ['type' => 'textarea', 'label' => 'Custom Header HTML/Scripts', 'default' => ''],
                    'custom_footer_code' => ['type' => 'textarea', 'label' => 'Custom Footer HTML/Scripts', 'default' => ''],
                ],
                'hooks_manifest' => ['head_custom_code', 'footer_custom_code'],
                'is_system' => true,
            ],
        ];

        foreach ($plugins as $plugin) {
            Plugin::firstOrCreate(['slug' => $plugin['slug']], $plugin);
        }
    }
}
