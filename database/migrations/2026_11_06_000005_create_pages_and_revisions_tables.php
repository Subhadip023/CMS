<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('template')->default('default');
            $table->string('status')->default('draft'); // draft, published, scheduled, archived
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_homepage')->default(false);
            $table->string('access_type')->default('public'); // public, password, members_only
            $table->string('access_password')->nullable();
            $table->integer('order_index')->default(0);
            $table->longText('layout_draft')->nullable(); // AST JSON for current working draft
            $table->longText('layout_published')->nullable(); // AST JSON for live published page
            $table->json('global_styles')->nullable(); // Page-specific style overrides
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['website_id', 'slug']);
            $table->index(['website_id', 'status']);
            $table->index(['website_id', 'is_homepage']);
            $table->index(['website_id', 'order_index']);
        });

        Schema::create('page_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('revision_number');
            $table->string('title');
            $table->longText('content'); // AST JSON snapshot
            $table->json('global_styles')->nullable();
            $table->boolean('is_published_snapshot')->default(false);
            $table->string('summary')->nullable(); // e.g. "Updated hero CTA button and color scheme"
            $table->timestamp('created_at')->useCurrent();

            $table->index(['page_id', 'revision_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_revisions');
        Schema::dropIfExists('pages');
    }
};
