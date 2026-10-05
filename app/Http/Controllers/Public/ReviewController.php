<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Menyimpan ulasan dari customer untuk produk di pesanan tertentu
     */
    public function store(Request $request, $invoiceNumber)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();

        // 1. Pastikan pesanan milik user yang sedang login dan berstatus completed
        $order = Order::where('invoice_number', $invoiceNumber)
            ->where('user_id', $userId)
            ->where('order_status', 'completed')
            ->firstOrFail();

        // 2. Pastikan produk memang ada di dalam pesanan tersebut
        $hasProduct = $order->items()->where('product_id', $validated['product_id'])->exists();
        if (!$hasProduct) {
            return back()->with('error', 'Produk ini tidak ada dalam pesanan Anda.');
        }

        // 3. Cek apakah ulasan sudah pernah dibuat (Mencegah error unique constraint)
        $existingReview = Review::where('order_id', $order->id)
            ->where('product_id', $validated['product_id'])
            ->where('user_id', $userId)
            ->exists();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk pada pesanan ini.');
        }

        // 4. Simpan ulasan
        Review::create([
            'order_id'   => $order->id,
            'product_id' => $validated['product_id'],
            'user_id'    => $userId,
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'] ?? null,
            'is_approved'=> true, // Default disetujui
        ]);

        return back()->with('success', 'Ulasan Anda berhasil dikirim!');
    }
}