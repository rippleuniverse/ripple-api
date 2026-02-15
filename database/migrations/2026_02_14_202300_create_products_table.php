<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_category_id')->nullable();
            $table->string('featured_image');
            $table->enum('type', ['physical', 'digital']);
            $table->string('title');
            $table->longText('description');
            $table->json('price');
            $table->longText('about');
            $table->json('benefits');
            $table->json('target_users');
            $table->json('how_to_use');
            $table->json('access_delivery');
            $table->timestamps();

            $table->foreign('product_category_id')->references('id')->on('product_categories')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
