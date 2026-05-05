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
        if (!Schema::hasColumn('course_comments', 'course_slug')) {
            Schema::table('course_comments', function (Blueprint $table) {
                $table->string('course_slug');
                $table->string('user_name')->default('Guest Student');
                $table->string('avatar')->nullable();
                $table->text('content');
                $table->integer('likes')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not necessary for a hotfix
    }
};
