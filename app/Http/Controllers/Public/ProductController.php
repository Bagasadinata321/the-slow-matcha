<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\EditorjsParser;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan Katalog Semua Produk (/products)
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('status', 'published') // Sesuai kolom 'status' di Migration
            ->with(['variants', 'coverMedia']);

        // Filter Berdasarkan Kategori / Tipe Produk
        if ($request->filled('category')) {
            $category = strtolower($request->query('category'));
            $query->where('product_type', $category);
        } elseif ($request->filled('type')) {
            $type = strtolower($request->query('type'));
            $query->where('product_type', $type);
        }

        $products = $query->orderBy('sort_order', 'asc')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('public.products.index', compact('products'));
    }

    /**
     * Menampilkan Detail Produk (/products/{slug})
     */
    public function show($slug, EditorjsParser $parser)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'published') // Sesuai kolom 'status' di Migration
            ->with([
                'variants' => function ($q) {
                    $q->orderBy('price', 'asc');
                }, 
                'coverMedia', 
                'galleryMedia'
            ])
            ->firstOrFail();
            // Mengambil 4 produk lain (misal dari tipe/kategori yang sama atau random)
            $relatedProducts = Product::with(['variants', 'coverMedia'])
                ->where('id', '!=', $product->id)
                ->latest()
                ->take(4)
                ->get();

        // Menggunakan kolom 'content' (bukan 'description') sesuai Migration
        $parsedContent = '';
        if (!empty($product->content)) {
            $rawContent = is_string($product->content) 
                ? json_decode($product->content, true) 
                : $product->content;

            if ($rawContent && (is_array($rawContent) || is_object($rawContent))) {
                $parsedContent = $parser->parse($rawContent);
            } else {
                $parsedContent = $product->content; // Fallback jika teks biasa
            }
        }

        // Ambil produk terkait berdasarkan product_type yang sama (opsional untuk rekomendasi di view)
        $relatedProducts = Product::where('status', 'published')
            ->where('product_type', $product->product_type)
            ->where('id', '!=', $product->id)
            ->with(['variants', 'coverMedia'])
            ->take(4)
            ->get();

        return view('public.products.show', compact('product', 'parsedContent', 'relatedProducts'));
    }
}