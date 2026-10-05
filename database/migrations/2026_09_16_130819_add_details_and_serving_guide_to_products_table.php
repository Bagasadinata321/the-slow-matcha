<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Menyisipkan kolom baru tanpa menghapus tabel
        Schema::table('products', function (Blueprint $table) {
            $table->text('details')->nullable()->after('content');
            $table->text('serving_guide')->nullable()->after('details');
        });
    }

    public function down()
    {
        // Membatalkan penambahan kolom jika di-rollback
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['details', 'serving_guide']);
        });
    }
};