# 🍵 TheSlowMatcha – E-Commerce & Management System

Platform e-commerce modern spesialis produk matcha premium berbasis **Laravel 12**. Sistem ini dirancang untuk menangani e-commerce retail dengan varian gramatur, keranjang belanja hybrid, transaksi atomic, hingga panel analitik admin.

## 🚀 Fitur Utama

### 🛒 Customer Storefront
- **Hybrid Cart System:** Mendukung keranjang belanja baik untuk Guest Session maupun Authenticated User.
- **Product Variant & Dynamic Pricing:** Pengelolaan gramatur, diskon promo, dan kalkulasi harga real-time.
- **Atomic Checkout Process:** Penguncian transaksi via `DB::transaction()` untuk menjamin ketersediaan stok dan integritas harga.
- **Order Tracking:** Fitur pencarian status pesanan publik via nomor invoice unik.

### 📊 Admin Management Panel
- **Dual-Axis Analytics Dashboard:** Grafik gabungan antara trafik pengunjung unik dan omzet penjualan mingguan/bulanan.
- **Polymorphic Media Management:** Pengolahan foto produk (Cover & Gallery) berbasis relasi polymorphic (`media_relations`).
- **Low Stock Warning & Quick Update:** Peringatan stok menipis (≤ 5 unit) serta pembaruan stok cepat via AJAX.
- **Linktree Builder:** Fitur mikro-landing page dinamis untuk media sosial.

## 🛠️ Tech Stack
- **Framework & Language:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL / MariaDB
- **Frontend:** Blade Templating Engine, Tailwind CSS
- **Media Handling:** Intervention Image v3
- **Authentication:** Laravel Breeze

## 💻 Cara Menjalankan di Lokal

```bash
# Clone repository
git clone [https://github.com/Bagasadinata321/the-slow-matcha.git](https://github.com/Bagasadinata321/the-slow-matcha.git)

# Masuk ke direktori proyek
cd the-slow-matcha

# Install dependency PHP
composer install

# Salin konfigurasi environment
cp .env.example .env

# Generate Application Key
php artisan key:generate

# Jalankan migrasi database
php artisan migrate

# Jalankan server lokal
php artisan serve
