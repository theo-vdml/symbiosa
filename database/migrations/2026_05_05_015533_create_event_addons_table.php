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
        Schema::create('event_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->index()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('price'); // En centimes

            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedInteger('reserved_count')->default(0);

            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();

            $table->unsignedInteger('max_per_order')->nullable();
            $table->integer('sort_order')->default(0)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_addons');
    }
};
