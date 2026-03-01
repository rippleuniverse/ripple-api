<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('payment_url')
                ->nullable();
            $table->string('trx_id')
                ->unique();
            $table->decimal('amount');
            $table->decimal('shipping_fee')->default(0);
            $table->json('billing_information');
            $table->enum('status', ['pending', 'paid', 'delivered', 'in_transit', 'cancelled'])
                ->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
