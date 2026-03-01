<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('currency', ['USD', 'NGN'])->default('NGN');
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->enum('currency', ['USD', 'NGN'])->default('NGN');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
