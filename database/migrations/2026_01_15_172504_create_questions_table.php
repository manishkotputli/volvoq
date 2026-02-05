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
        Schema::create('questions', function (Blueprint $table) {
    $table->id();

    // relations
    $table->foreignId('category_id')
        ->constrained()
        ->cascadeOnDelete();

    // content
    $table->string('title');
    $table->string('slug')->unique();
    $table->longText('body')->nullable();

    // lifecycle (PHASE-2.1)
    $table->enum('status', [
        'draft',
        'pending',
        'review',
        'published',
        'failed'
    ])->default('draft');

    // control
    $table->foreignId('created_by')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->timestamp('published_at')->nullable();

    // meta
    $table->json('extra')->nullable();

    $table->timestamps();

    $table->index(['category_id', 'status']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
