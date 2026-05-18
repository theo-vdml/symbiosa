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
        Schema::create('checkin_list_reservables', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('checkin_list_id')->constrained()->cascadeOnDelete();
            $blueprint->morphs('reservable');
            $blueprint->unique(['checkin_list_id', 'reservable_type', 'reservable_id'], 'checkin_list_reservable_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkin_list_reservables');
    }
};
