<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'event_category_id')) {
                $table->unsignedBigInteger('event_category_id')->nullable();
                $table->foreign('event_category_id')->references('id')->on('event_categories')->onDelete('SET NULL');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'event_category_id')) {
                $table->dropForeign(['event_category_id']);
                $table->dropColumn('event_category_id');
            }
        });
    }
};
