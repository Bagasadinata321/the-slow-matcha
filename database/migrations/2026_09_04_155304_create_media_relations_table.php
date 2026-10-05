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
        Schema::create('media_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();

            // Menyiapkan struktur Polymorphic Relation bawaan Laravel
            $table->string('entity_type', 50); // Menampung nama Model, contoh: 'App\Models\Product'
            $table->unsignedInteger('entity_id'); // ID dari tabel produk atau homepage_hero
            $table->string('usage', 50); // Kegunaan, contoh: cover, gallery, video_fallback

            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_relations');
    }
};
