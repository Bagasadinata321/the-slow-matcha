<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Helper privat untuk mengambil cart aktif
     */
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->first();
        }

        $sessionId = Session::getId();
        return Cart::where('session_id', $sessionId)->first();
    }

    /**
     * Halaman Checkout
     */
    public function index()
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('public.cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $cartItems = $cart->items()->with(['product.coverMedia', 'variant'])->get();

        // Hitung Subtotal
        $subtotal = $cartItems->sum(function ($item) {
            $price = ($item->variant->promo_price && $item->variant->promo_price < $item->variant->price)
                ? $item->variant->promo_price
                : $item->variant->price;

            return $price * $item->quantity;
        });

        // Ambil alamat tersimpan jika user terautentikasi
        $savedAddresses = Auth::check() 
            ? Address::where('user_id', Auth::id())->orderByDesc('is_primary')->get() 
            : collect();

        $primaryAddress = $savedAddresses->firstWhere('is_primary', true) ?? $savedAddresses->first();

        return view('public.checkout.index', compact(
            'cart',
            'cartItems',
            'subtotal',
            'savedAddresses',
            'primaryAddress'
        ));
    }

    /**
     * Memproses transaksi Checkout & Membuat Order Snapshot
     */
    public function process(Request $request)
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('public.cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        // Validasi Form Checkout (Termasuk penambahan address_id)
        $validated = $request->validate([
            'address_id'       => 'nullable|exists:addresses,id',
            'customer_name'    => 'required|string|max:100',
            'customer_email'   => 'required|email|max:100',
            'customer_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'province'         => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'district'         => 'nullable|string|max:100',
            'postal_code'      => 'nullable|string|max:10',
            'courier'          => 'required|string|max:50',
            'shipping_cost'    => 'required|numeric|min:0',
            'payment_method'   => 'required|string|max:50',
            'save_address'     => 'nullable|boolean',
        ]);

        $cartItems = $cart->items()->with(['product', 'variant'])->get();

        // Validasi stok ulang sebelum transaksi diproses
        foreach ($cartItems as $item) {
            if ($item->variant->stock < $item->quantity) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Stok untuk varian {$item->product->title} ({$item->variant->variant_name}) tidak mencukupi.");
            }
        }

        // Hitung ulang Subtotal di Server (Keamanan)
        $subtotal = $cartItems->sum(function ($item) {
            $price = ($item->variant->promo_price && $item->variant->promo_price < $item->variant->price)
                ? $item->variant->promo_price
                : $item->variant->price;

            return $price * $item->quantity;
        });

        $shippingCost = (float) $validated['shipping_cost'];
        $totalAmount  = $subtotal + $shippingCost;

        // Buat Transaksi Database (Atomic)
        try {
            $order = DB::transaction(function () use ($validated, $cart, $cartItems, $subtotal, $shippingCost, $totalAmount) {
                
                // 1. Generate Invoice Number Unik (misal: TSM202609230001)
                $invoiceNumber = 'TSM' . date('Ymd') . Str::padLeft(Order::whereDate('created_at', today())->count() + 1, 4, '0');

                // Format Alamat Pengiriman Lengkap
                $fullShippingAddress = $validated['shipping_address'];
                if (!empty($validated['district']) || !empty($validated['city']) || !empty($validated['province'])) {
                    $fullShippingAddress .= "\n" . implode(', ', array_filter([
                        $validated['district'] ?? null,
                        $validated['city'] ?? null,
                        $validated['province'] ?? null,
                        $validated['postal_code'] ?? null,
                    ]));
                }

                // 2. Simpan Data Order
                $order = Order::create([
                    'invoice_number'   => $invoiceNumber,
                    'user_id'          => Auth::id(),
                    'customer_name'    => $validated['customer_name'],
                    'customer_email'   => $validated['customer_email'],
                    'customer_phone'   => $validated['customer_phone'],
                    'shipping_address' => $fullShippingAddress,
                    'shipping_courier' => $validated['courier'],
                    'subtotal'         => $subtotal,
                    'shipping_cost'    => $shippingCost,
                    'total_amount'     => $totalAmount,
                    'payment_method'   => $validated['payment_method'],
                    'payment_status'   => 'unpaid',
                    'order_status'     => 'pending',
                ]);

                // 3. Simpan Order Items Snapshot & Potong Stok Varian
                foreach ($cartItems as $item) {
                    $price = ($item->variant->promo_price && $item->variant->promo_price < $item->variant->price)
                        ? $item->variant->promo_price
                        : $item->variant->price;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $item->product_id,
                        'variant_id'   => $item->variant_id,
                        'product_name' => $item->product->title,
                        'gram_size'    => $item->variant->gram_size ?? 0,
                        'price'        => $price,
                        'quantity'     => $item->quantity,
                        'subtotal'     => $price * $item->quantity,
                    ]);

                    // Decrement Stok Varian
                    $item->variant->decrement('stock', $item->quantity);
                }

                // 4. Opsi Simpan Alamat Baru jika User Logged-in & mencentang "Simpan Alamat"
                if (Auth::check() && !empty($validated['save_address'])) {
                    Address::create([
                        'user_id'        => Auth::id(),
                        'label'          => 'Alamat Checkout',
                        'recipient_name' => $validated['customer_name'],
                        'phone_number'   => $validated['customer_phone'],
                        'full_address'   => $validated['shipping_address'],
                        'province'       => $validated['province'] ?? null,
                        'city'           => $validated['city'] ?? null,
                        'district'       => $validated['district'] ?? null,
                        'postal_code'    => $validated['postal_code'] ?? null,
                        'is_primary'     => Address::where('user_id', Auth::id())->count() === 0,
                    ]);
                }

                // 5. Kosongkan Keranjang Belanja
                $cart->items()->delete();

                return $order;
            });

            // Redirect Sukses ke Halaman Nota / Status Checkout
            return redirect()->route('public.checkout.success', ['invoice_number' => $order->invoice_number]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Success Checkout (/checkout/success/{invoice_number})
     */
    public function success($invoice_number)
    {
        $order = Order::with('items')->where('invoice_number', $invoice_number)->firstOrFail();

        return view('public.checkout.success', compact('order'));
    }
}