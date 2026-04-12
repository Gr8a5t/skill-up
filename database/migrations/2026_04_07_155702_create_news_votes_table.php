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
        Schema::create('news_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_post_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->index();
            $table->tinyInteger('vote_type'); // 1 for upvote, -1 for downvote
            $table->timestamps();

            // Ensure a user (session) can only vote once per post in either direction
            $table->unique(['news_post_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_votes');
    }
};
