<?php 
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index(Request $request)
    {
        $orderNumber = $request->query('order_number');
        $order = null;

        if ($orderNumber) {
            $order = Order::with(['items.product', 'items.variant'])
                ->where('invoice_number', $orderNumber)
                ->first();

            if ($order) {
                // Formatting data dummy tracking step untuk UI timeline
                $order->trackingSteps = collect([
                    (object)[
                        'label'          => 'Pesanan Dibuat',
                        'is_done'        => true,
                        'is_current'     => $order->order_status === 'pending',
                        'date'           => $order->created_at,
                        'estimated_date' => null
                    ],
                    (object)[
                        'label'          => 'Diproses',
                        'is_done'        => in_array($order->order_status, ['processing', 'shipped', 'completed']),
                        'is_current'     => $order->order_status === 'processing',
                        'date'           => null,
                        'estimated_date' => 'Penjual menyiapkan barang'
                    ],
                    (object)[
                        'label'          => 'Dalam Pengiriman',
                        'is_done'        => in_array($order->order_status, ['shipped', 'completed']),
                        'is_current'     => $order->order_status === 'shipped',
                        'date'           => null,
                        'estimated_date' => 'Kurir membawa paket'
                    ],
                    (object)[
                        'label'          => 'Selesai',
                        'is_done'        => $order->order_status === 'completed',
                        'is_current'     => $order->order_status === 'completed',
                        'date'           => null,
                        'estimated_date' => 'Paket diterima'
                    ],
                ]);

                // Map variabel UI tambahan
                $order->courier = $order->shipping_courier ?? 'JNE';
                $order->courier_tracking_url = '#';
                $order->status_label = strtoupper($order->order_status);

                foreach ($order->items as $item) {
                    $item->name = $item->product_name;
                    $item->variant_label = $item->gram_size ? $item->gram_size . 'g' : 'Standard';
                    $item->qty = $item->quantity;
                    $item->image_url = $item->product && $item->product->coverMedia 
                        ? asset('storage/' . $item->product->coverMedia->path) 
                        : asset('images/placeholder.jpg');
                }
            }
        }

        return view('public.orders.track', compact('order'));
    }
}