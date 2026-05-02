<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // For guest tracking if needed
            $table->string('course_slug');
            $table->string('video_id'); // YouTube Video ID
            $table->float('progress_seconds')->default(0);
            $table->float('total_seconds')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            // Unique index to prevent duplicate records for a user/guest watching the same video in a course
            $table->unique(['user_id', 'session_id', 'course_slug', 'video_id'], 'user_vid_progress_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_lesson_progress');
    }
};
