<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders', 'total_amount');

        // 1. Filter Pencarian (Nama, Email, Phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // 2. Filter Status Akun (Aktif / Blokir)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', true);
            } elseif ($request->status === 'blocked') {
                $query->where('status', false);
            }
        }

        // 3. Filter Tier LTV (VIP > Rp 400.000, Regular <= Rp 400.000)
        if ($request->filled('tier')) {
            if ($request->tier === 'vip') {
                $query->has('orders')->having('orders_sum_total_amount', '>=', 400000);
            } elseif ($request->tier === 'regular') {
                $query->where(function($q) {
                    $q->doesntHave('orders')
                      ->orHaving('orders_sum_total_amount', '<', 400000);
                });
            }
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = User::with(['orders' => function($q) {
            $q->latest();
        }])->where('role', 'customer')->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * FITUR 2: Toggle Block / Unblock Status Akun Pelanggan
     */
    public function toggleStatus($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->status = !$customer->status;
        $customer->save();

        $statusText = $customer->status ? 'diaktifkan kembali' : 'diblokir';
        return redirect()->back()->with('success', "Akun pelanggan {$customer->name} berhasil {$statusText}.");
    }

    /**
     * FITUR 4: Ekspor Data Pelanggan ke CSV
     */
    public function exportCsv(Request $request)
    {
        $fileName = 'data-pelanggan-' . date('Y-m-d') . '.csv';

        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders', 'total_amount');

        // Terapkan filter yang sama saat diekspor
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $customers = $query->latest()->get();

        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($customers) {
            $file = fopen('php://output', 'w');
            // Header Kolom CSV
            fputcsv($file, ['ID', 'Nama Pelanggan', 'Email', 'No. WhatsApp', 'Total Pesanan', 'Total Belanja (LTV)', 'Status Akun', 'Tanggal Daftar']);

            foreach ($customers as $cust) {
                fputcsv($file, [
                    $cust->id,
                    $cust->name,
                    $cust->email,
                    $cust->phone ?? '-',
                    $cust->orders_count,
                    'Rp ' . number_format($cust->orders_sum_total_amount ?? 0, 0, ',', '.'),
                    $cust->status ? 'Aktif' : 'Diblokir',
                    $cust->created_at->format('Y-m-d H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}