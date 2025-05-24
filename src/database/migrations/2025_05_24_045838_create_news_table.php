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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary');
            $table->text('description');
            $table->string('url')->unique();
            $table->string('source');
            $table->string('ext_id')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();

            $table->index('created_at');
            $table->index(['source', 'published_at']);
            $table->unique(['source', 'ext_id']);
            $table->fullText(['title', 'summary', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
