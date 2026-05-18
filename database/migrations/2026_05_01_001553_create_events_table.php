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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->string('city');
            $table->string('country');
            $table->text('address')->nullable();
            $table->string('dress_code')->nullable();
            $table->integer('minimum_age')->nullable();
            $table->text('description');
            $table->text('body')->nullable();
            $table->json('faq')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_visible_in_archives')->default(false);
            $table->timestamp('published_at')->nullable();

            $table->timestamp('ticketing_starts_at')->nullable();
            $table->timestamp('ticketing_ends_at')->nullable();
            $table->text('ticket_email_content')->nullable();
            $table->text('ticket_pdf_content')->nullable();
            $table->json('stripe_metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
