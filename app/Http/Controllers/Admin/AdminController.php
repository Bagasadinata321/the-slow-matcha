<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Menampilkan daftar semua ulasan di Admin Panel
     */
    public function index()
    {
        $reviews = Review::with(['user:id,name', 'product:id,title', 'order:id,invoice_number'])
            ->latest()
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Menyimpan balasan admin untuk ulasan customer
     */
    public function reply(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $review->update([
            'admin_reply' => $validated['admin_reply'],
            'replied_at'  => now(),
        ]);

        return back()->with('success', 'Balasan ulasan berhasil disimpan.');
    }

    /**
     * Toggle status tampil/sembunyi ulasan (is_approved)
     */
    public function toggleApproval(Review $review)
    {
        $review->update([
            'is_approved' => !$review->is_approved,
        ]);

        return back()->with('success', 'Status visibilitas ulasan berhasil diperbarui.');
    }
}