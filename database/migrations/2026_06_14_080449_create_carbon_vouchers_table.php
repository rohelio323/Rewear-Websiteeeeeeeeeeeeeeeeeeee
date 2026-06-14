<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carbon_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount_amount', 10, 2);
            $table->integer('co2_cost');
            $table->integer('quantity_available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('carbon_vouchers');
    }
};
