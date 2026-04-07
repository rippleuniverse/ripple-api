<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('open_roles', function (Blueprint $table) {
            $table->enum('type', ['full_time', 'part_time', 'internship', 'contract'])
                ->nullable()
                ->default(null)
                ->change();
            $table->enum('experience_level', ['beginner', 'intermediate', 'expert'])
                ->nullable()
                ->default(null)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('open_roles', function (Blueprint $table) {
            $table->enum('type', ['full_time', 'part_time', 'internship', 'contract'])
                ->change();
            $table->enum('experience_level', ['beginner', 'intermediate', 'expert'])
                ->change();
        });
    }
};
