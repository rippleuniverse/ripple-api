<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('open_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name');
            $table->string('company_location');
            $table->enum('type', ['full_time', 'part_time', 'internship', 'contract']);
            $table->enum('experience_level', ['beginner', 'intermediate', 'expert']);
            $table->enum('style', ['remote', 'on_site', 'hybrid']);
            $table->string('salary');
            $table->longText('description');
            $table->longText('about_company');
            $table->json('responsibilities');
            $table->json('requirements');
            $table->json('benefits');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_roles');
    }
};
