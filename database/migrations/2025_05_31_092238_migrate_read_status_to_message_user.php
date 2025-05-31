<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all messages that have been read
        $readMessages = DB::table('messages')
            ->whereNotNull('read_at')
            ->get();

        // Insert them into the message_user table
        foreach ($readMessages as $message) {
            DB::table('message_user')->insert([
                'message_id' => $message->id,
                'user_id' => $message->user_id,
                'read_at' => $message->read_at,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to do anything in down() as we're not removing any data
    }
}; 