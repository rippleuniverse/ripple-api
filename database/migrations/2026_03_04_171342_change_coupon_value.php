<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {

            $table->dropColumn('value');
            $table->decimal('percentage_value')->default(0);
            $table->json('fixed_value');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('percentage_value');
            $table->dropColumn('fixed_value');
            $table->decimal('value')->default(0);
        });
    }
};
