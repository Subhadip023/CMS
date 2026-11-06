<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Site creator / owner
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('category')->default('general'); // business, portfolio, agency, ecommerce, blog, etc.
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, maintenance, suspended
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->json('settings')->nullable(); // General settings: timezone, language, social links, custom code
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('status');
            $table->index('is_published');
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('verification_token')->nullable();
            $table->string('ssl_status')->default('pending'); // pending, active, failed
            $table->timestamps();

            $table->index('website_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
        Schema::dropIfExists('websites');
    }
};
