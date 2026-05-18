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
        Schema::create('checkin_lists', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('event_id')->constrained()->cascadeOnDelete();
            $blueprint->string('name');
            $blueprint->string('public_url_token')->unique();
            $blueprint->string('public_url_password')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkin_lists');
    }
};
