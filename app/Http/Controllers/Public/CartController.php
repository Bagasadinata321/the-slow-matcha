<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Helper privat untuk mengambil atau membuat instance Keranjang Aktif
     */
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = Session::getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Halaman Utama Keranjang Belanja (/cart)
     */
    public function index()
    {
        $cart = $this->getCart();
        $cartItems = $cart->items()->with(['product.coverMedia', 'variant'])->get();

        // Hitung Subtotal
        $subtotal = $cartItems->sum(function ($item) {
            $price = ($item->variant->promo_price && $item->variant->promo_price < $item->variant->price)
                ? $item->variant->promo_price
                : $item->variant->price;

            return $price * $item->quantity;
        });

        // Threshold Gratis Ongkir (Misal: Rp 500.000)
        $freeShippingThreshold = 500000;
        $progressPercent = min(100, round(($subtotal / $freeShippingThreshold) * 100));
        $remainingForFreeShipping = max(0, $freeShippingThreshold - $subtotal);

        return view('public.cart.index', compact(
            'cart',
            'cartItems',
            'subtotal',
            'freeShippingThreshold',
            'progressPercent',
            'remainingForFreeShipping'
        ));
    }

    /**
     * Tambah Produk/Varian ke Keranjang
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($validated['variant_id']);

        // Cek stok varian produk
        if ($variant->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak mencukupi (sisa: ' . $variant->stock . ')'
            ], 422);
        }

        $cart = $this->getCart();

        // Cek apakah item varian ini sudah ada di keranjang
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $variant->product_id)
            ->where('variant_id', $variant->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];
            
            // Validasi total kuantitas terhadap stok
            if ($variant->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuantitas melebihi stok yang tersedia.'
                ], 422);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'quantity'   => $validated['quantity'],
            ]);
        }

        // Ambil total item unik untuk update badge keranjang di navbar
        $totalItems = $cart->items()->count();

        return response()->json([
            'success'     => true,
            'message'     => 'Produk berhasil ditambahkan ke keranjang!',
            'total_items' => $totalItems
        ]);
    }

    /**
     * Update Kuantitas Item di Keranjang
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::with('variant')->findOrFail($id);

        if ($cartItem->variant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Kuantitas melebihi stok yang tersedia.',
            ], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Keranjang diperbarui.',
        ]);
    }

    /**
     * Hapus Item dari Keranjang
     */
    public function destroy($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang.',
        ]);
    }
}