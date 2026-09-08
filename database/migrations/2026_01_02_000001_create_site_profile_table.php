<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Single-row table: one doctor, one profile. We don't enforce a hard
    // "only one row" constraint at the DB level - the model/controller
    // always fetch-or-create id=1, which is simpler than a DB check
    // constraint and totally sufficient for this use case.
    public function up(): void
    {
        Schema::create('site_profile', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('credentials')->nullable();       // e.g. "MBChB, MPH"
            $table->string('title')->nullable();              // e.g. "Physician & Digital Health Consultant"
            $table->text('bio')->nullable();
            $table->text('short_bio')->nullable();             // for cards/previews
            $table->unsignedInteger('years_experience')->nullable();
            $table->json('specialties')->nullable();           // ["Internal Medicine", "Digital Health", ...]
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('booking_url')->nullable();         // Calendly-style link for "Book a Consultation"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_profile');
    }
};
