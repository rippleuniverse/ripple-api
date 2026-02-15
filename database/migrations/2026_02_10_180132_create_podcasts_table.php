<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->string('featured_image');
            $table->string('title');
            $table->longText('description');
            $table->unsignedBigInteger('podcast_category_id')->nullable();
            $table->string('audio');
            $table->bigInteger('duration_in_minutes');
            $table->timestamps();

            $table->foreign('podcast_category_id')->references('id')->on('podcast_categories')
                ->onDelete('SET NULL');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
