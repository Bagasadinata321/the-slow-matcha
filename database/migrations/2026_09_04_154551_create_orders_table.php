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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique(); // Nomor invoice transaksi unik

            // Relasi ke user. nullOnDelete() menjaga data riwayat transaksi jika akun customer dihapus
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('customer_name', 100);
            $table->string('customer_email', 100);
            $table->string('customer_phone', 20);
            $table->text('shipping_address');
            $table->string('shipping_courier', 50)->nullable(); // <-- TAMBAHKAN INI (e.g. JNE - REG)

            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);

            $table->string('payment_method', 50); // e.g. midtrans, qris, manual
            $table->enum('payment_status', ['unpaid', 'paid', 'failed', 'expired'])->default('unpaid');
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->string('tracking_number', 100)->nullable(); // Resi pengiriman
            $table->timestamps();

            // Indexing untuk mempercepat query laporan/filter di Admin Panel
            $table->index('payment_status');
            $table->index('order_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
