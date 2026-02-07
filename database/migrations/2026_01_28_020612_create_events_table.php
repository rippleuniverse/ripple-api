<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('featured_image');
            $table->json('images');
            $table->string('title');
            $table->longText('description');
            $table->date('date');
            $table->enum('access', ['free', 'paid']);
            $table->enum('type', ['physical', 'online']);
            $table->unsignedBigInteger('event_category_id')->nullable();
            $table->timestamps();

            $table->foreign('event_category_id')->references('id')->on('event_categories')
                ->onDelete('SET NULL');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
