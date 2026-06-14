<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->enum('status', ['pending', 'payment_confirmed', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 12, 2);
            $table->decimal('co2_saved_amount', 8, 2)->default(0.00);
            $table->string('payment_reference')->nullable();
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('voucher_redemption_id')->nullable()->constrained('voucher_redemptions');
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
