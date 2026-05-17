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
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['background', 'poster']);
        });

        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropColumn('logo');
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('background')->nullable();
            $table->string('poster')->nullable();
        });

        Schema::table('sponsors', function (Blueprint $table) {
            $table->string('logo')->nullable();
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->string('thumbnail')->nullable();
        });
    }
};
