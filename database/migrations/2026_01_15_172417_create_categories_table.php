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
       Schema::create('categories', function (Blueprint $table) {
    $table->id();

    // hierarchy
    $table->foreignId('parent_id')
        ->nullable()
        ->constrained('categories')
        ->nullOnDelete();

    $table->unsignedTinyInteger('level')->default(0);
    // 0 = super, 1 = category, 2 = sub, 3 = child...

    // core
    $table->string('name');
    $table->string('slug')->unique();

    // SEO
    $table->string('seo_title')->nullable();
    $table->text('seo_description')->nullable();

    // control
    $table->boolean('is_active')->default(true);
    $table->unsignedInteger('sort_order')->default(0);

    // meta
    $table->json('extra')->nullable();

    $table->timestamps();

    $table->index(['parent_id', 'level', 'is_active']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
