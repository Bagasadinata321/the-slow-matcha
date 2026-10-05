<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\Media;
use App\Models\MediaRelation;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // 1. Menampilkan Daftar Produk
    public function index()
    {
        $products = Product::with(['variants', 'coverMedia'])->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    // 2. Menampilkan Form Tambah Produk
    public function create()
    {
        return view('admin.products.edit');
    }

    // 3. Menyimpan Produk & Varian Baru
    public function store(StoreProductRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // 1. Simpan Produk Utama
                $product = Product::create([
                    'title'         => $request->title,
                    'slug'          => Str::slug($request->title),
                    'product_type'  => $request->product_type,
                    'excerpt'       => Str::limit(strip_tags($request->description), 150),
                    'content'       => $request->description,
                    'details'       => $request->details,
                    'serving_guide' => $request->serving_guide,
                    'status'        => 'published',
                ]);

                // 2. Upload Cover Utama
                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    $imageData = ImageService::compressAndUpload($request->file('image'), 'products/covers', 800, 80);
                    $media = Media::create($imageData);

                    MediaRelation::create([
                        'media_id'    => $media->id,
                        'entity_type' => 'product',
                        'entity_id'   => $product->id,
                        'usage'       => 'cover',
                    ]);
                }

                // 3. Upload Galeri (Multiple Images)
                if ($request->hasFile('gallery')) {
                    foreach ($request->file('gallery') as $file) {
                        if ($file->isValid()) {
                            $imageData = ImageService::compressAndUpload($file, 'products/gallery', 1200, 75);
                            $media = Media::create($imageData);

                            MediaRelation::create([
                                'media_id'    => $media->id,
                                'entity_type' => 'product',
                                'entity_id'   => $product->id,
                                'usage'       => 'gallery',
                            ]);
                        }
                    }
                }

                // 4. Simpan Varian Produk
                if ($request->has('variants')) {
                    foreach ($request->variants as $v) {
                        $price = (float) $v['price'];
                        $discountPrice = !empty($v['discount_price']) ? (float) $v['discount_price'] : null;
                        $isPromo = $discountPrice && $discountPrice < $price;
                        $discountPercent = $isPromo ? (($price - $discountPrice) / $price) * 100 : 0;
                        $variantName = !empty($v['variant_name']) ? trim($v['variant_name']) : 'Standard';

                        $product->variants()->create([
                            'variant_name'     => $variantName,
                            'price'            => $price,
                            'is_promo'         => $isPromo ? 1 : 0,
                            'discount_percent' => round($discountPercent, 2),
                            'promo_price'      => $isPromo ? $discountPrice : null,
                            'stock'            => $v['stock'],
                        ]);
                    }
                }
            });

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            Log::error('Error Store Product: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        }
    }

    // 4. Menampilkan Form Edit Produk
    public function edit($id)
    {
        $product = Product::with(['variants', 'coverMedia', 'galleryMedia'])->findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    // 5. Memperbarui Data Produk & Media
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'title'                     => 'required|string|max:150',
            'product_type'              => 'required|string',
            'description'               => 'nullable|string',
            'details'                   => 'nullable|string',
            'serving_guide'             => 'nullable|string',
            'image'                     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'gallery'                   => 'nullable|array',
            'gallery.*'                 => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'variants.*.price'          => 'required|numeric|min:0',
            'variants.*.discount_price' => 'nullable|numeric|lt:variants.*.price',
            'variants.*.stock'          => 'required|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {
                // 1. Update Produk Utama
                $product->update([
                    'title'         => $request->title,
                    'slug'          => Str::slug($request->title),
                    'product_type'  => $request->product_type,
                    'excerpt'       => Str::limit(strip_tags($request->description), 150),
                    'content'       => $request->description,
                    'details'       => $request->details,
                    'serving_guide' => $request->serving_guide,
                ]);

                // 2. Upload Cover Utama Jika Ada
                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    $imageData = ImageService::compressAndUpload($request->file('image'), 'products/covers', 800, 80);
                    $media = Media::create($imageData);

                    MediaRelation::create([
                        'media_id'    => $media->id,
                        'entity_type' => 'product',
                        'entity_id'   => $product->id,
                        'usage'       => 'cover',
                    ]);
                }

                // 3. Upload Galeri (Multiple Images) Jika Ada
                if ($request->hasFile('gallery')) {
                    foreach ($request->file('gallery') as $file) {
                        if ($file->isValid()) {
                            $imageData = ImageService::compressAndUpload($file, 'products/gallery', 1200, 75);
                            $media = Media::create($imageData);

                            MediaRelation::create([
                                'media_id'    => $media->id,
                                'entity_type' => 'product',
                                'entity_id'   => $product->id,
                                'usage'       => 'gallery',
                            ]);
                        }
                    }
                }

                // 4. Sync Varian Produk
                $product->variants()->delete();
                if ($request->has('variants')) {
                    foreach ($request->variants as $v) {
                        $price = (float) $v['price'];
                        $discountPrice = !empty($v['discount_price']) ? (float) $v['discount_price'] : null;
                        $isPromo = $discountPrice && $discountPrice < $price;
                        $discountPercent = $isPromo ? (($price - $discountPrice) / $price) * 100 : 0;
                        $variantName = !empty($v['variant_name']) ? trim($v['variant_name']) : 'Standard';

                        $product->variants()->create([
                            'variant_name'     => $variantName,
                            'price'            => $price,
                            'is_promo'         => $isPromo ? 1 : 0,
                            'discount_percent' => round($discountPercent, 2),
                            'promo_price'      => $isPromo ? $discountPrice : null,
                            'stock'            => $v['stock'],
                        ]);
                    }
                }
            });

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error Update Product: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    // 6. Hapus Produk beserta Media dan Variannya
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        DB::transaction(function () use ($product) {
            $this->deleteMediaByUsage($product->id, 'cover');
            $this->deleteMediaByUsage($product->id, 'gallery');

            $product->delete();
        });

        return redirect()->route('admin.products.index')->with('success', 'Produk dan seluruh medianya berhasil dihapus!');
    }

    public function deleteMediaByUsageRoute($productId, $usage)
    {
        $this->deleteMediaByUsage((int) $productId, $usage);

        return response()->json([
            'success' => true,
            'message' => 'Media berhasil dihapus!'
        ]);
    }

    public function deleteSingleMedia($id)
    {
        $media = Media::find($id);

        if ($media) {
            if (Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }

            MediaRelation::where('media_id', $media->id)->delete();
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gambar tidak ditemukan.'
        ], 404);
    }

    private function deleteMediaByUsage(int $productId, string $usage): void
    {
        $relations = MediaRelation::where('entity_type', 'product')
            ->where('entity_id', $productId)
            ->where('usage', $usage)
            ->get();

        foreach ($relations as $rel) {
            $media = Media::find($rel->media_id);
            if ($media) {
                Storage::disk('public')->delete($media->path);
                $media->delete();
            }
            $rel->delete();
        }
    }

    public function uploadEditorImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('editor-images', 'public');

            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => asset('storage/' . $path),
                ]
            ]);
        }

        return response()->json([
            'success' => 0,
            'message' => 'Gagal mengunggah gambar'
        ], 400);
    }
}