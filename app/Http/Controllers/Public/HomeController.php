<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    /**
     * Menampilkan Landing Page / Homepage.
     */
    public function index()
    {
        // 1. Ambil produk berstatus published beserta varian & media cover
        $featuredProducts = Product::with(['variants', 'coverMedia', 'reviews'])
            ->where('status', 'published')
            ->latest()
            ->take(6)
            ->get();

        // 2. Ambil ulasan asli dari database (yang is_approved = true)
        $realReviews = Review::where('is_approved', true)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->with(['user', 'product'])
            ->latest()
            ->take(6)
            ->get();

        // 3. Jika ulasan asli masih kosong/belum ada, buatkan fallback dummy agar layout slider tetap simetris (3 card)
        if ($realReviews->isEmpty()) {
            $latestReviews = collect([
                (object)[
                    'rating' => 5,
                    'comment' => 'Rasa umami-nya benar-benar terasa dan sama sekali tidak pahit. Sangat pas dibuat Matcha Latte setiap pagi.',
                    'user' => (object)['name' => 'Aninda R.'],
                    'product' => (object)['title' => 'Ceremonial Matcha']
                ],
                (object)[
                    'rating' => 5,
                    'comment' => 'Warna hijaunya sangat cerah khas Ceremonial Grade Jepang. Teksturnya sangat halus saat di-whisk.',
                    'user' => (object)['name' => 'Dimas P.'],
                    'product' => (object)['title' => 'Uji Premium Matcha']
                ],
                (object)[
                    'rating' => 5,
                    'comment' => 'Kemasan dan kualitas matcha terbaik yang pernah saya beli secara online di Indonesia. 10/10!',
                    'user' => (object)['name' => 'Sarah K.'],
                    'product' => (object)['title' => 'Daily Ritual Matcha']
                ],
            ]);
        } else {
            $latestReviews = $realReviews;
        }

        // 4. Kirim kedua variabel ke view public.home
        return view('public.home', compact('featuredProducts', 'latestReviews'));
    }

    /**
     * Menampilkan Halaman About.
     */
    public function about()
    {
        return view('public.about');
    }
}