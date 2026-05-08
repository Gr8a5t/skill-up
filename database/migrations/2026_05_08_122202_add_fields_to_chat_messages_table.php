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
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'sender_id')) {
                $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('chat_messages', 'recipient_id')) {
                $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('chat_messages', 'message')) {
                $table->text('message')->nullable();
            }
            if (!Schema::hasColumn('chat_messages', 'is_read')) {
                $table->boolean('is_read')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            //
        });
    }
};
