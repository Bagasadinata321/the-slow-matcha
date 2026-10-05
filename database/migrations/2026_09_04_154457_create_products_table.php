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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary Key AUTO_INCREMENT

            $table->string('title', 150);
            $table->string('slug', 150)->unique();

            // Penambahan Jenis Produk (Matcha vs Tool)
            $table->enum('product_type', ['matcha', 'tool'])->default('matcha');

            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable(); // Ruang JSON untuk Editor.js

            $table->enum('status', ['published', 'draft'])->default('published');
            $table->integer('sort_order')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
