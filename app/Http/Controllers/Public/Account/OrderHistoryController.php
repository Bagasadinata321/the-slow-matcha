<?php
namespace App\Http\Controllers\Public\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    /**
     * Tampilkan Riwayat Pesanan (/account/orders)
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Order::with('items')
            ->where('user_id', Auth::id())
            ->latest();

        // Filter berdasarkan status pesanan
        if ($status && in_array($status, ['pending', 'processing', 'shipped', 'completed', 'cancelled'])) {
            $query->where('order_status', $status);
        }

        $orders = $query->paginate(5)->withQueryString();

        // FIX: Menunjuk langsung ke resources/views/public/account/orders.blade.php
        return view('public.account.orders', compact('orders', 'status'));
    }

    /**
     * Detail Pesanan (/account/orders/{invoice_number})
     */
    public function show($invoice_number)
    {
        $order = Order::with(['items.product', 'items.variant'])
            ->where('user_id', Auth::id())
            ->where('invoice_number', $invoice_number)
            ->firstOrFail();

        return view('public.account.order-detail', compact('order'));
    }

    /**
     * Beli Lagi / Re-order (/account/orders/{invoice_number}/reorder)
     */
    public function reorder($invoice_number)
    {
        $order = Order::with('items')
            ->where('user_id', Auth::id())
            ->where('invoice_number', $invoice_number)
            ->firstOrFail();

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        foreach ($order->items as $item) {
            $cartItem = $cart->items()
                ->where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $item->quantity);
            } else {
                $cart->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity'   => $item->quantity,
                ]);
            }
        }

        return redirect()->route('public.cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang belanja.');
    }
}