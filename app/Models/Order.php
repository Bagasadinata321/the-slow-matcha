<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'shipping_courier',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'tracking_number',
    ];

    // 1 Transaksi/Order memiliki BANYAK item rincian belanjaan
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Transaksi ini milik 1 Customer (bisa null jika user terhapus/guest)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    // Tambahkan di dalam class Order (App\Models\Order.php)
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
