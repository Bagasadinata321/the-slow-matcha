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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename'); // Nama file unik di storage
            $table->string('original_filename'); // Nama file asli saat diunggah
            $table->string('path'); // Path penyimpanan
            $table->string('mime_type', 50)->default('image/webp');
            $table->integer('file_size'); // Ukuran byte
            $table->timestamp('created_at')->useCurrent(); // Hanya pakai created_at tanpa updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
