<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->json('what_to_expect');
            $table->json('who_to_expect');
            $table->json('facilitators');
            $table->json('agendas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('what_to_expect');
            $table->dropColumn('who_to_expect');
            $table->dropColumn('facilitators');
            $table->dropColumn('agendas');
        });
    }
};
