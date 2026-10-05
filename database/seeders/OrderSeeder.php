<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Customer Pertama
        $customer1 = User::firstOrCreate(
            ['email' => 'budi@gmail.com'],
            [
                'name'     => 'Budi Santoso',
                'username' => 'budisantoso',
                'phone'    => '081234567890',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'status'   => true,
            ]
        );

        // 2. Buat User Customer Kedua
        $customer2 = User::firstOrCreate(
            ['email' => 'siti@gmail.com'],
            [
                'name'     => 'Siti Rahma',
                'username' => 'sitirahma',
                'phone'    => '087890123456',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'status'   => true,
            ]
        );

        // ----------------------------------------------------
        // Transaksi Customer 1 (Budi)
        // ----------------------------------------------------
        $order1 = Order::create([
            'invoice_number'   => 'INV-' . date('Ymd') . '-001',
            'user_id'          => $customer1->id, // Menghubungkan ke User Budi
            'customer_name'    => $customer1->name,
            'customer_phone'   => $customer1->phone,
            'shipping_address' => 'Jl. Udayana No. 12, Mataram, NTB',
            'subtotal'         => 150000,
            'shipping_cost'    => 15000,
            'total_amount'     => 165000,
            'payment_status'   => 'unpaid',
            'order_status'     => 'pending',
            'payment_method'   => 'qris',
        ]);

        OrderItem::create([
            'order_id'     => $order1->id,
            'product_name' => 'Matcha Powder Premium Ceremonial',
            'gram_size'    => 100,
            'price'        => 150000,
            'quantity'     => 1,
            'subtotal'     => 150000,
        ]);

        // ----------------------------------------------------
        // Transaksi Customer 2 (Siti)
        // ----------------------------------------------------
        $order2 = Order::create([
            'invoice_number'   => 'INV-' . date('Ymd') . '-002',
            'user_id'          => $customer2->id, // Menghubungkan ke User Siti
            'customer_name'    => $customer2->name,
            'customer_phone'   => $customer2->phone,
            'shipping_address' => 'Jl. Raya Senggigi No. 45, Lombok Barat, NTB',
            'subtotal'         => 280000,
            'shipping_cost'    => 20000,
            'total_amount'     => 300000,
            'payment_status'   => 'paid',
            'order_status'     => 'processing',
            'payment_method'   => 'bank_transfer',
        ]);

        OrderItem::create([
            'order_id'     => $order2->id,
            'product_name' => 'Matcha Latte Pack 500g',
            'gram_size'    => 500,
            'price'        => 280000,
            'quantity'     => 1,
            'subtotal'     => 280000,
        ]);

        // Tambahan Transaksi Kedua milik Siti untuk Uji Coba LTV & Histori Multitransaksi
        $order3 = Order::create([
            'invoice_number'   => 'INV-' . date('Ymd') . '-003',
            'user_id'          => $customer2->id, // Pesanan ke-2 milik Siti
            'customer_name'    => $customer2->name,
            'customer_phone'   => $customer2->phone,
            'shipping_address' => 'Jl. Raya Senggigi No. 45, Lombok Barat, NTB',
            'subtotal'         => 150000,
            'shipping_cost'    => 15000,
            'total_amount'     => 165000,
            'payment_status'   => 'paid',
            'order_status'     => 'completed',
            'payment_method'   => 'qris',
        ]);

        OrderItem::create([
            'order_id'     => $order3->id,
            'product_name' => 'Matcha Powder Premium Ceremonial',
            'gram_size'    => 100,
            'price'        => 150000,
            'quantity'     => 1,
            'subtotal'     => 150000,
        ]);
    }
}