<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            
            // Identitas Toko
            $table->string('site_name')->default('TheSlowMatcha');
            $table->string('site_tagline')->nullable()->default('Pure Matcha, Better Days.');
            $table->text('address')->nullable();
            $table->string('contact_phone')->nullable()->default('6281234567890');
            $table->string('contact_email')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            
            // Hero Section Settings (Pengaturan Video Hero)
            $table->string('hero_title')->nullable()->default('Nikmati Matcha Premium Khas Jepang');
            $table->text('hero_subtitle')->nullable()->default('Koleksi rasa otentik untuk momen terbaikmu setiap hari.');
            $table->string('hero_video_path')->nullable(); // Upload file video lokal (MP4/WebM)
            $table->string('hero_video_url')->nullable();  // URL video eksternal (YouTube/CDN)
            
            // SEO & Social Links
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->text('site_description')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};