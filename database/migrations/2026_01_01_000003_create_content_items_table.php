<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();

            // article | research | project | tool | resource
            $table->string('type')->index();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status')->default('draft')->index(); // draft | published
            $table->timestamp('published_at')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('og_image')->nullable();

            // type-specific fields live here, e.g.
            // research: {"author_name":"", "publication_date":"", "findings":"", "source_url":""}
            // project:  {"problem":"", "solution":"", "technology":"", "status":"prototype"}
            // tool:     {"input_schema": [...], "clinical_disclaimer": ""}
            $table->jsonb('extra')->nullable();

            $table->unsignedInteger('read_time_minutes')->nullable();

            $table->timestamps();

            $table->index(['type', 'status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
};
