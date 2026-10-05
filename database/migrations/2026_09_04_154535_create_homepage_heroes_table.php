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
        Schema::create('homepage_hero', function (Blueprint $table) {
            $table->id();
            $table->string('headline'); // Judul utama hero section
            $table->text('subheadline')->nullable(); // Deskripsi pendek
            $table->string('video_url'); // URL video latar hero
            $table->string('fallback_image_url')->nullable(); // Gambar cadangan jika video tidak load
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Mengikuti DDL SQL[cite: 1]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_heroes');
    }
};
