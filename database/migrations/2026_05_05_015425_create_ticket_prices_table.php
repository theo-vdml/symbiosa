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
        Schema::create('ticket_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_type_id')->index()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('price'); // En centimes

            // Conditions de validité de la phase
            $table->dateTime('available_until')->nullable();
            $table->unsignedInteger('threshold')->nullable();

            $table->integer('sort_order')->default(0)->index();

            $table->timestamps();

            $table->index(['ticket_type_id', 'sort_order', 'available_until'], 'idx_active_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_prices');
    }
};
