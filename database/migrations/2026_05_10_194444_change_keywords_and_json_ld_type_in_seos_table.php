<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seos', function (Blueprint $table) {
            $table->json('keywords')->nullable()->change();
            // json_ld is already json, but making sure
            $table->json('json_ld')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('seos', function (Blueprint $table) {
            $table->string('keywords')->nullable()->change();
        });
    }
};
