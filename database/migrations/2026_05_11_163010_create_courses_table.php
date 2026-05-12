<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category');
            $table->string('level'); // Beginner, Medium, Advance
            $table->string('icon')->default('book-outline');
            $table->string('color')->default('#f5f5f5');
            $table->string('playlist_id')->nullable(); // YouTube Playlist ID
            $table->text('recap')->nullable();
            $table->json('key_concepts')->nullable(); // Store as JSON array
            $table->string('source_files_url')->nullable();
            $table->string('cheatsheet_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
