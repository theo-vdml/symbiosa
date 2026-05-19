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
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id();
            $table->string('hero_preheading')->default('DJ Sets & Expériences');
            $table->string('hero_title')->default('Symbiosa');
            $table->string('hero_subheading')->default('Belgique — Est. 2026');
            $table->string('spotify_playlist_heading')->nullable();
            $table->string('spotify_playlist_id')->nullable();
            $table->boolean('show_spotify_playlist')->default(false);
            $table->boolean('spotify_playlist_force_dark')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_pages');
    }
};
