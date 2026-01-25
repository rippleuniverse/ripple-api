<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('author');
            $table->longText('skills');
            $table->enum('experience_level', ['beginner', 'intermediate', 'expert']);
            $table->unsignedBigInteger('program_category_id')->nullable();
            $table->decimal('price');
            $table->string('featured_image');
            $table->timestamps();

            $table->foreign('program_category_id')->references('id')->on('program_categories')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
