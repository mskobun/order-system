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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("description");
            $table->string("image_url");
            $table->float("price");
            $table->boolean("available");
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string("name");
        });
        Schema::create('items_categories', function (Blueprint $table) {
            $table->foreignId("item_id");
            $table->foreignId("category_id");
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('items_categories');
    }
};
