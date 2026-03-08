<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropIfExists();
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_no');
            $table->unsignedBigInteger('program_id');
            $table->string('title');
            $table->longText('description')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')
                ->onDelete('cascade');
        });
    }
};
