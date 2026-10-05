<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'name'     => 'Admin TheSlowMatcha',
            'username' => 'admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('Admin123'),
            'phone'    => '081234567890',
            'role'     => 'admin',
            'status'   => true,
        ]);

        // 2. Akun Pembeli (Customer)
        User::create([
            'name'     => 'Bagas Adinata',
            'username' => 'bagas',
            'email'    => 'customer@gmail.com',
            'password' => Hash::make('password123'),
            'phone'    => '087750292514',
            'role'     => 'customer',
            'status'   => true,
        ]);

        // 3. Data Produk Matcha & Varian
        $matchaProduct = Product::create([
            'title'        => 'Uji Ceremonial Grade Matcha',
            'slug'         => Str::slug('Uji Ceremonial Grade Matcha'),
            'product_type' => 'matcha',
            'excerpt'      => 'Matcha kualitas seremonial premium diimpor langsung dari Uji, Kyoto.',
            'content'      => json_encode(['blocks' => []]),
            'status'       => 'published',
            'sort_order'   => 1,
        ]);

        ProductVariant::create([
            'product_id'       => $matchaProduct->id,
            'variant_name'     => '30g',
            'price'            => 180000.00,
            'is_promo'         => true,
            'discount_percent' => 10.00,
            'promo_price'      => 162000.00,
            'stock'            => 50,
        ]);

        ProductVariant::create([
            'product_id'       => $matchaProduct->id,
            'variant_name'     => '100g',
            'price'            => 450000.00,
            'is_promo'         => false,
            'discount_percent' => 0.00,
            'promo_price'      => null,
            'stock'            => 25,
        ]);

        // 4. Data Produk Aksesori (Tool) & Varian
        $toolProduct = Product::create([
            'title'        => 'Bamboo Whisk (Chasen)',
            'slug'         => Str::slug('Bamboo Whisk Chasen'),
            'product_type' => 'tool',
            'excerpt'      => 'Pengocok matcha tradisional bahan bambu murni dengan 100 prongs.',
            'content'      => json_encode(['blocks' => []]),
            'status'       => 'published',
            'sort_order'   => 2,
        ]);

        ProductVariant::create([
            'product_id'       => $toolProduct->id,
            'variant_name'     => 'Standard Chasen',
            'price'            => 120000.00,
            'is_promo'         => false,
            'discount_percent' => 0.00,
            'promo_price'      => null,
            'stock'            => 15,
        ]);
    }
}