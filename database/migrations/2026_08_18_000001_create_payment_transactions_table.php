<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50);
            $table->string('transaction_id', 191);
            $table->unsignedInteger('MaDH');
            $table->decimal('amount', 15, 2);
            $table->timestamp('processed_at');
            $table->timestamps();

            $table->unique(['provider', 'transaction_id']);
            $table->foreign('MaDH')->references('MaDH')->on('donhang')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
