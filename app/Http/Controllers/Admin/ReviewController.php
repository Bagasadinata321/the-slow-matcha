<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Menampilkan daftar ulasan dengan filter & pencarian
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product', 'order'])->latest();

        // Filter berdasarkan Bintang (1-5)
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search berdasarkan Nama Produk, Invoice, atau Nama Pelanggan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', fn($p) => $p->where('title', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('order', fn($o) => $o->where('invoice_number', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Menyimpan atau memperbarui balasan resmi penjual
     */
    public function reply(Request $request, Review$review)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $review->update([
            'admin_reply' => $request->admin_reply,
            'replied_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balasan berhasil disimpan!',
            'admin_reply' => $review->admin_reply,
            'replied_at' => $review->replied_at->format('d M Y, H:i')
        ]);
    }

    /**
     * Mengubah Status Tampil/Sembunyi Review
     */
    public function toggleStatus(Review $review)
    {
        $review->update([
            'is_approved' => !$review->is_approved
        ]);

        return response()->json([
            'success' => true,
            'is_approved' => $review->is_approved,
            'message' => $review->is_approved ? 'Ulasan berhasil ditampilkan.' : 'Ulasan berhasil disembunyikan.'
        ]);
    }
}