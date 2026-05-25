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
        Schema::create('news_pages', function (Blueprint $table) {
            $table->id();
            $table->string('preheading')->nullable();
            $table->string('heading');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('events_pages', function (Blueprint $table) {
            $table->id();
            $table->string('preheading')->nullable();
            $table->string('heading');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('archive_pages', function (Blueprint $table) {
            $table->id();
            $table->string('preheading')->nullable();
            $table->string('heading');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('lost_ticket_pages', function (Blueprint $table) {
            $table->id();
            $table->string('preheading')->nullable();
            $table->string('heading');
            $table->text('description')->nullable();
            $table->json('help_items')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_ticket_pages');
        Schema::dropIfExists('archive_pages');
        Schema::dropIfExists('events_pages');
        Schema::dropIfExists('news_pages');
    }
};
