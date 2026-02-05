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
        Schema::create('entity_meta', function (Blueprint $table) {
    $table->id();

    // polymorphic relation
    $table->string('entity_type');  
    // App\Models\Question | Answer | Category | Tag | ScrapedQuestion | AIJob

    $table->unsignedBigInteger('entity_id');

    // meta
    $table->string('meta_key');
    $table->json('meta_value')->nullable();

    // control
    $table->boolean('is_active')->default(true);

    $table->timestamps();

    $table->index(['entity_type', 'entity_id']);
    $table->index('meta_key');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_meta');
    }
};
