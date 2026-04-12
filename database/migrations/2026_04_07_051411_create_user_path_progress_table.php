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
        Schema::create('user_path_progress', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable(); // Track for guests
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('path_slug');
            $table->integer('module_index');
            $table->boolean('completed')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'path_slug', 'module_index']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_path_progress');
    }
};
