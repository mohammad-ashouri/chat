<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();

            // Ensure one draft per user per chat
            $table->unique(['user_id', 'chat_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('drafts');
    }
};
