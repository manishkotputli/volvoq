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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();

            // 🔗 Relations
            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnDelete();

            // 🧠 Core content
            $table->longText('content');

            // 🔥 Answer classification
            $table->enum('answer_type', [
                'short',
                'detailed',
                'code',
                'beginner',
                'advanced',
                'ai_generated'
            ])->default('detailed');

            // ⭐ Priority & ordering
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            // 🔄 Lifecycle control
            $table->enum('status', [
                'draft',
                'review',
                'published',
                'rejected'
            ])->default('review');

            // 🤖 AI / automation ready
            $table->boolean('is_ai_generated')->default(false);
            $table->string('ai_model')->nullable();
            $table->json('ai_meta')->nullable();

            // 👤 Tracking
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 📊 Engagement (future)
            $table->unsignedBigInteger('upvotes')->default(0);
            $table->unsignedBigInteger('downvotes')->default(0);

            // 🧩 Flexible future fields
            $table->json('extra')->nullable();

            $table->timestamps();

            // ⚡ Indexing for CRORE SCALE
            $table->index(['question_id', 'status']);
            $table->index(['is_primary', 'sort_order']);
            $table->index(['answer_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
