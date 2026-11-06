<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('version')->default('1.0.0');
            $table->string('author')->default('Antigravity Core');
            $table->string('preview_image')->nullable();
            $table->json('default_config')->nullable(); // Default tokens: primary, secondary, font families, radius
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('website_theme_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('theme_id')->nullable()->constrained('themes')->nullOnDelete();
            $table->json('tokens')->nullable(); // Live customized colors, fonts, spacing tokens
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->timestamps();

            $table->unique('website_id');
        });

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('business'); // business, portfolio, agency, ecommerce, blog, corporate, landing, personal, education
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->longText('layout_data'); // Complete page AST JSON
            $table->boolean('is_system')->default(true);
            $table->foreignId('website_id')->nullable()->constrained('websites')->cascadeOnDelete();
            $table->timestamps();

            $table->index('category');
        });

        Schema::create('reusable_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('category')->default('custom'); // hero, features, cta, testimonials, team, faq, forms
            $table->string('thumbnail')->nullable();
            $table->longText('tree_data'); // Component subtree AST JSON
            $table->timestamps();

            $table->index(['website_id', 'category']);
        });

        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('version')->default('1.0.0');
            $table->text('description')->nullable();
            $table->string('author')->default('Antigravity Ecosystem');
            $table->json('settings_schema')->nullable();
            $table->json('hooks_manifest')->nullable();
            $table->boolean('is_system')->default(true);
            $table->timestamps();
        });

        Schema::create('website_plugins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('plugin_id')->constrained('plugins')->cascadeOnDelete();
            $table->boolean('is_enabled')->default(false);
            $table->json('config')->nullable();
            $table->timestamps();

            $table->unique(['website_id', 'plugin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_plugins');
        Schema::dropIfExists('plugins');
        Schema::dropIfExists('reusable_components');
        Schema::dropIfExists('templates');
        Schema::dropIfExists('website_theme_settings');
        Schema::dropIfExists('themes');
    }
};
