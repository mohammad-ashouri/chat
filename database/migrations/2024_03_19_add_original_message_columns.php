<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('original_message_id')->nullable()->after('is_system')->constrained('messages')->nullOnDelete();
            $table->foreignId('original_sender_id')->nullable()->after('original_message_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['original_message_id']);
            $table->dropForeign(['original_sender_id']);
            $table->dropColumn(['original_message_id', 'original_sender_id']);
        });
    }
}; 