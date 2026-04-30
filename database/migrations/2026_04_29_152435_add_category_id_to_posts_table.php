<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the column as nullable to avoid errors with existing data
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Create a default category if it doesn't exist
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'General',
            'slug' => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign all existing posts to this category
        DB::table('posts')->update(['category_id' => $categoryId]);

        // Now make it non-nullable
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
