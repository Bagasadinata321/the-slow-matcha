<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'role',
        'status',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi (JSON/Array).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data untuk kolom spesifik.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'boolean',
        ];
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Memeriksa apakah user memiliki role Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Memeriksa apakah user memiliki role Customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    // ==========================================
    // RELASI DATABASE
    // ==========================================

    /**
     * Relasi ke Modul Pesanan (Orders)
     * 1 User dapat memiliki banyak transaksi Order.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relasi ke Buku Alamat (Addresses)
     * 1 User dapat menyimpan beberapa alamat pengiriman.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Relasi ke Keranjang Belanja (Cart)
     * 1 User hanya memiliki 1 Cart aktif.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }
}