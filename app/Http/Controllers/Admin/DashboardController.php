<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Default filter disesuaikan ke 7_days agar grafik langsung tampil cantik
        $filter = $request->get('filter', '7_days');
        $startDate = null;
        $endDate = null;

        // 1. Penyesuaian Filter Periode Tanggal
        switch ($filter) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case '7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case '30_days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                } else {
                    $startDate = Carbon::now()->subDays(6)->startOfDay();
                    $endDate = Carbon::now()->endOfDay();
                }
                break;
            default:
                $filter = '7_days';
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
        }

        // 2. Metrik Utama (KPI Cards)
        $totalSales = DB::table('orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $pendingOrdersCount = DB::table('orders')
            ->where('payment_status', 'unpaid')
            ->orWhere('order_status', 'pending')
            ->count();

        $todayVisitors = DB::table('visitor_logs')
            ->whereBetween('visited_at', [Carbon::today(), Carbon::today()->endOfDay()])
            ->distinct('ip_address')
            ->count('ip_address');

        $totalVisitors = DB::table('visitor_logs')
            ->whereBetween('visited_at', [$startDate, $endDate])
            ->distinct('ip_address')
            ->count('ip_address');

        // 3. Kalkulasi Pertumbuhan Pengunjung ($visitorGrowth)
        $diffInDays = max(1, $startDate->diffInDays($endDate) + 1);
        $prevStartDate = $startDate->copy()->subDays($diffInDays);
        $prevEndDate = $startDate->copy()->subSecond();

        $previousVisitors = DB::table('visitor_logs')
            ->whereBetween('visited_at', [$prevStartDate, $prevEndDate])
            ->distinct('ip_address')
            ->count('ip_address');

        if ($previousVisitors > 0) {
            $visitorGrowth = round((($totalVisitors - $previousVisitors) / $previousVisitors) * 100, 1);
        } else {
            $visitorGrowth = $totalVisitors > 0 ? 100 : 0;
        }

        // Stok barang menipis
        $lowStockCount = DB::table('product_variants')
            ->where('stock', '<=', 5)
            ->count();

        // 4. Data Grafik Dual-Axis (Sales & Visitors per Hari)
        $salesPerDay = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date')
            ->toArray();

        $visitorsPerDay = DB::table('visitor_logs')
            ->select(
                DB::raw('DATE(visited_at) as date'),
                DB::raw('COUNT(DISTINCT ip_address) as total')
            )
            ->whereBetween('visited_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(visited_at)'))
            ->pluck('total', 'date')
            ->toArray();

        $labels = [];
        $salesData = [];
        $visitorData = [];

        $period = new \DatePeriod(
            $startDate->copy(),
            new \DateInterval('P1D'),
            $endDate->copy()->addDay()
        );

        foreach ($period as $dt) {
            $formattedDate = $dt->format('Y-m-d');
            $labels[] = $dt->format('d M');
            $salesData[] = (float) ($salesPerDay[$formattedDate] ?? 0);
            $visitorData[] = (int) ($visitorsPerDay[$formattedDate] ?? 0);
        }

        $chartData = [
            'labels' => $labels,
            'sales' => $salesData,
            'visitors' => $visitorData,
        ];

        // 5. Analytics Tambahan
        // Menggunakan User Agent atau Dummy Fallback karena referer_source tidak ada di migration
        $trafficSources = [
            'Direct / Mobile' => DB::table('visitor_logs')->whereBetween('visited_at', [$startDate, $endDate])->where('user_agent', 'like', '%Mobile%')->count(),
            'Direct / Desktop' => DB::table('visitor_logs')->whereBetween('visited_at', [$startDate, $endDate])->where('user_agent', 'not like', '%Mobile%')->count(),
        ];

        // Halaman Terpopuler
        $topPagesData = DB::table('visitor_logs')
            ->select('visited_page', DB::raw('COUNT(*) as total'))
            ->whereBetween('visited_at', [$startDate, $endDate])
            ->groupBy('visited_page')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 6. Seksi Operasional
        $recentOrders = DB::table('orders')
            ->latest('created_at')
            ->take(5)
            ->get();

        $lowStockItems = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'product_variants.id',
                'products.title as product_name',
                'product_variants.variant_name',
                'product_variants.stock'
            )
            ->where('product_variants.stock', '<=', 5)
            ->take(5)
            ->get();

        // 7. Top 5 Produk Terlaris
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'products.title as product_name',
                'product_variants.variant_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->where('orders.payment_status', 'paid')
            ->groupBy('product_variants.id', 'products.title', 'product_variants.variant_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'filter',
            'startDate',
            'endDate',
            'totalSales',
            'pendingOrdersCount',
            'todayVisitors',
            'visitorGrowth',
            'lowStockCount',
            'chartData',
            'trafficSources',
            'topPagesData',
            'recentOrders',
            'lowStockItems',
            'topProducts'
        ));
    }

    public function trafficStats()
    {
        $topPages = DB::table('visitor_logs')
            ->select('visited_page', DB::raw('COUNT(*) as total'))
            ->groupBy('visited_page')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'top_pages' => $topPages]);
    }

    public function lowStockItems()
    {
        $items = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'product_variants.id',
                'products.title as product_name',
                'product_variants.variant_name',
                'product_variants.stock'
            )
            ->where('product_variants.stock', '<=', 5)
            ->get();

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function quickUpdateStock(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'stock' => 'required|integer|min:0',
        ]);

        DB::table('product_variants')
            ->where('id', $request->variant_id)
            ->update(['stock' => $request->stock]);

        return response()->json(['success' => true, 'message' => 'Stok varian berhasil diperbarui!']);
    }
}