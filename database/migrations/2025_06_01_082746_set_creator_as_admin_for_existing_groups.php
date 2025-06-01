<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Chat;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set all group creators as admins
        $chats = Chat::all();
        foreach ($chats as $chat) {
            DB::table('chat_user')
                ->where('chat_id', $chat->id)
                ->where('user_id', $chat->user_id)
                ->update(['is_admin' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all admin flags
        DB::table('chat_user')->update(['is_admin' => false]);
    }
};
