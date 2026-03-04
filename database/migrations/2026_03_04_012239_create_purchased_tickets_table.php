<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchased_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->unsignedBigInteger('event_ticket_id')->nullable();
            $table->unsignedBigInteger('invoice_id');
            $table->string('quantity');
            $table->string('unit_price');
            $table->timestamps();

            $table->foreign('event_ticket_id')->references('id')->on('event_tickets')
                ->onDelete('SET NULL');
            $table->foreign('invoice_id')->references('id')->on('invoices')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchased_tickets');
    }
};
