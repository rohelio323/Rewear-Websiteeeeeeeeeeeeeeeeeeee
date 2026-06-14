<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('voucher_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('voucher_id')->constrained('carbon_vouchers');
            $table->decimal('co2_deducted', 10, 2);
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('voucher_redemptions');
    }
};

