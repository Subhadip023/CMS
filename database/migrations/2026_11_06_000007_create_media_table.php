<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('filename');
            $table->string('original_name');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // bytes
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->string('folder')->default('/');
            $table->json('responsive_variants')->nullable(); // thumbnail, medium, large URLs
            $table->timestamps();
            $table->softDeletes();

            $table->index(['website_id', 'folder']);
            $table->index(['website_id', 'mime_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
