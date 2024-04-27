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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->enum('type', ['DELIVERY', 'DINE_IN']);
            $table->string('address');
            $table->string('phone');
            $table->string('name');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->foreignId('order_id');
            $table->foreignId('item_id');
            $table->integer('amount');
        });

        Schema::create('order_status', function (Blueprint $table) {
            $table->foreignId('order_id');
            $table->dateTime('created_at');
            $table->enum('status', ['PAYMENT_PENDING', 'ACCEPTED', 'COOKING', 'CANCELLED', 'COMPLETED', 'DELIVERY_WAITING_FOR_PICKUP', 'DELIVERY_ON_THE_ROAD']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
