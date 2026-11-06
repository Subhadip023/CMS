<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('location')->default('header'); // header, footer, mobile, sidebar
            $table->timestamps();

            $table->unique(['website_id', 'slug']);
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('title');
            $table->string('url')->default('#');
            $table->string('type')->default('custom'); // custom, page, post, category
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('target')->default('_self'); // _self, _blank
            $table->integer('order_index')->default(0);
            $table->timestamps();

            $table->index(['menu_id', 'order_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
