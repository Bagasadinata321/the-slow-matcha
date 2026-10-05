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
        Schema::create('linktree_items', function (Blueprint $table) {
            $table->id();
            // Self-referencing Foreign Key (menunjuk ke id di tabel ini juga untuk sub-menu)[cite: 1]
            $table->foreignId('parent_id')->nullable()->constrained('linktree_items')->cascadeOnDelete();

            $table->string('label', 100); // Teks tombol[cite: 1]
            $table->boolean('has_sub')->default(false); // 1 = Punya sub-menu, 0 = Tunggal[cite: 1]
            $table->string('url')->nullable(); // URL tujuan[cite: 1]
            $table->string('icon', 50)->nullable(); // Nama class icon[cite: 1]
            $table->integer('sort_order')->default(1); // Urutan tampil[cite: 1]
            $table->boolean('is_active')->default(true); // Status tombol (aktif/nonaktif)[cite: 1]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linktree_items');
    }
};
