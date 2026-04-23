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
        Schema::create('chat_messages', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->integer('MaKH')->nullable(); // Lưu nếu khách đã đăng nhập
            $table->string('session_id')->nullable(); // Lưu cho khách vãng lai
            $table->text('message');
            $table->enum('sender', ['user', 'ai', 'admin'])->default('user');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
