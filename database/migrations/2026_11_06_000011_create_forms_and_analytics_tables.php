<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('form_id')->default('default'); // ID identifier inside the builder component
            $table->string('form_name')->default('Contact Form');
            $table->json('fields_data'); // key-value submitted inputs
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['website_id', 'is_read']);
            $table->index(['website_id', 'created_at']);
        });

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('visitor_hash', 64);
            $table->string('session_id', 64);
            $table->string('event_type')->default('pageview'); // pageview, click, form_submit
            $table->string('path')->default('/');
            $table->string('referrer')->nullable();
            $table->string('device_type')->default('desktop'); // desktop, tablet, mobile
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->unsignedInteger('duration')->default(0); // seconds on page
            $table->timestamp('created_at')->useCurrent();

            $table->index(['website_id', 'created_at']);
            $table->index(['website_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('form_submissions');
    }
};
