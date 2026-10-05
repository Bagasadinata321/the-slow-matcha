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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel products. 
            // cascadeOnDelete() artinya jika produk dihapus, seluruh varian gramnya otomatis terhapus.
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('variant_name');
            $table->decimal('price', 12, 2); // Harga normal
            $table->boolean('is_promo')->default(false); // Status diskon (0/1)
            $table->decimal('discount_percent', 5, 2)->default(0.00); // % diskon
            $table->decimal('promo_price', 12, 2)->nullable(); // Harga promo
            $table->integer('stock')->default(0);

            // Mencegah duplikasi varian gram yang sama dalam 1 produk (misal: 2x 50gr di 1 produk)
            $table->unique(['product_id', 'variant_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
