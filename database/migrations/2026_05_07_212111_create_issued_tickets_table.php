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
            $blueprint->foreignId('checkout_id')->constrained()->cascadeOnDelete();
            $blueprint->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            
            // Helpful to have direct access to what it is
            $blueprint->morphs('reservable');
            
            $blueprint->string('qr_code_token')->unique();
            $blueprint->timestamp('scanned_at')->nullable();
            
            // Flag to distinguish attendees (Tickets) from extras (Addons)
            $blueprint->boolean('is_attendee')->default(true);
            
            // Useful info for the ticket itself
            $blueprint->string('name'); // Name of the ticket/addon
            $blueprint->integer('price_paid'); // in cents
            
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
