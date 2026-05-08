<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issued_tickets', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('event_id')->constrained()->cascadeOnDelete();
            $blueprint->foreignId('checkout_id')->constrained()->cascadeOnDelete();
            // $blueprint->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();

            $blueprint->morphs('reservable');
            $blueprint->foreignId('ticket_price_id')->nullable()->constrained()->cascadeOnDelete();

            $blueprint->string('public_id')->unique();
            // $blueprint->string('qr_code_token')->unique();
            $blueprint->timestamp('checked_in_at')->nullable();

            $blueprint->integer('price_paid');

            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issued_tickets');
    }
};
