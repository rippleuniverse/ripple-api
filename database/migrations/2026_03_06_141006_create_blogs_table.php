<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('blog_category_id')->nullable();
            $table->text('description');
            $table->longText('content');
            $table->string('featured_image');
            $table->string('author');
            $table->timestamps();

            $table->foreign('blog_category_id')->references('id')->on('blog_categories')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
