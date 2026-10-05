@extends('layouts.admin')

@section('content')
<!-- Inject CDN Bootstrap 5 & Icons (Sebaiknya dipindah ke layout utama jika memungkinkan) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<div class="container-fluid px-4 pt-4 pb-4">
    
    {{-- Header & Realtime Badge --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Dashboard Analytics</h1>
            <p class="text-muted small mb-0">Performa operasional & statistik The Slow Matcha hari ini.</p>
        </div>
        <span class="badge bg-success px-3 py-2 rounded-pill fs-7">
            <i class="bi bi-clock-history me-1"></i> Realtime Sync
        </span>
    </div>

    {{-- SEKSI 1: HEADER & KPI CARDS (TOP BANNER) --}}
    <div class="row g-3 mb-4">
        
        {{-- KPI 1: Pengunjung Situs --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 card-hover style-cursor" onclick="openTrafficModal()">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Pengunjung Situs</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1">{{ number_format($todayVisitors) }}</h3>
                            <span class="badge bg-{{ $visitorGrowth >= 0 ? 'success' : 'danger' }}-subtle text-{{$visitorGrowth >= 0 ? 'success' : 'danger' }} mt-2">
                                <i class="bi bi-arrow-{{ $visitorGrowth >= 0 ? 'up' : 'down' }}-right me-1"></i>
                                {{ number_format(abs($visitorGrowth), 1) }}% vs periode lalu
                            </span>
                        </div>
                        <div class="icon-shape bg-primary text-white rounded-circle p-3">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2: Total Penjualan --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 h-100 card-hover">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Total Penjualan</span>
                                <h3 class="fw-bold mb-0 text-success mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                                <span class="badge bg-success-subtle text-success mt-2">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Transaksi lunas
                                </span>
                            </div>
                            <div class="icon-shape bg-success text-white rounded-circle p-3">
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- KPI 3: Pesanan Masuk --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 h-100 card-hover">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-semibold text-uppercase">Pesanan Masuk</span>
                                <h3 class="fw-bold mb-0 text-warning mt-1">{{ number_format($pendingOrdersCount) }}</h3>
                                <span class="badge bg-warning-subtle text-warning mt-2">
                                    <i class="bi bi-exclamation-circle me-1"></i> Butuh diproses
                                </span>
                            </div>
                            <div class="icon-shape bg-warning text-white rounded-circle p-3">
                                <i class="bi bi-bag-clock fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- KPI 4: Stok Menipis --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 card-hover style-cursor" onclick="openLowStockModal()">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Stok Menipis</span>
                            <h3 class="fw-bold mb-0 text-danger mt-1">{{ number_format($lowStockCount) }}</h3>
                            <span class="badge bg-danger-subtle text-danger mt-2">
                                <i class="bi bi-pencil-square me-1"></i> Update stok cepat
                            </span>
                        </div>
                        <div class="icon-shape bg-danger text-white rounded-circle p-3">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- SEKSI 2: ANALYTICS & DUAL-AXIS CHART (DENGAN FILTER TANGGAL) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-transparent border-0 pt-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h5 class="fw-bold mb-0">Analytics & Tren Performa</h5>
                <small class="text-muted">Perbandingan Pengunjung (Traffic) vs Penjualan (Omzet)</small>
            </div>
            
            {{-- Tombol Filter Tanggal --}}
            <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center gap-2" id="filterForm">
                <input type="hidden" name="filter" id="filterInput" value="{{ $filter }}">
                
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary {{ $filter == '7_days' ? 'active' : '' }}" onclick="setFilter('7_days')">7 Hari</button>
                    <button type="button" class="btn btn-outline-secondary {{ $filter == '30_days' ? 'active' : '' }}" onclick="setFilter('30_days')">30 Hari</button>
                    <button type="button" class="btn btn-outline-secondary {{ $filter == 'custom' ? 'active' : '' }}" onclick="toggleCustomDate()">Custom</button>
                </div>

                {{-- Custom Date Picker Form --}}
                <div id="customDateContainer" class="d-none d-flex align-items-center gap-1">
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    <span class="small text-muted">-</span>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="row g-4">
                {{-- Dual-Axis Line Chart --}}
                <div class="col-12 col-lg-8">
                    <div style="height: 280px;">
                        <canvas id="dualAxisChart" data-chart='@json($chartData)'></canvas>
                    </div>
                </div>
                {{-- Breakdowns: Donut & Top Pages --}}
                <div class="col-12 col-lg-4">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-12">
                            <h6 class="fw-bold small text-muted text-uppercase mb-2">Sumber Pengunjung</h6>
                            <div style="height: 120px;">
                                <canvas id="donutChart" data-donut='@json($trafficSources)'></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-12">
                            <h6 class="fw-bold small text-muted text-uppercase mb-2">Halaman Terpopuler</h6>
                            <div style="height: 120px;">
                                <canvas id="topPagesChart" data-toppages='@json($topPagesData)'></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEKSI 3: ACTIONABLE WIDGETS (OPERASIONAL) --}}
    <div class="row g-4 mb-4">
        
        {{-- Tabel 5 Pesanan Terbaru --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Pesanan Terbaru</h5>
                    <a href="{{ route('admin.orders.index') }}" class="text-decoration-none small fw-semibold">Kelola Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Invoice</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
<tr>
    <td class="ps-4 fw-bold">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-decoration-none text-dark">
            #{{ $order->invoice_number ?? $order->id }}
        </a>
    </td>
    <td>Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</td>
    <td>
        @php $status = $order->order_status ?? 'pending'; @endphp
        <span class="badge bg-{{ $status == 'completed' ? 'success' : ($status == 'processing' ? 'info' : ($status == 'shipped' ? 'primary' : 'warning')) }}">
            {{ ucfirst($status) }}
        </span>
    </td>
</tr>
@empty
<tr><td colspan="3" class="text-center py-4 text-muted">Belum ada pesanan terbaru.</td></tr>
@endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget Peringatan Stok & Update Cepat --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Peringatan Stok</h5>
                    <button class="btn btn-sm btn-outline-danger" onclick="openLowStockModal()">Update Semua</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Varian Matcha</th>
                                    <th style="width: 100px;">Stok</th>
                                    <th style="width: 110px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockItems as $item)
<tr>
    <td class="ps-4">
        <strong class="text-dark">{{ $item->product_name }}</strong><br>
        <small class="text-muted">{{ $item->variant_name }}</small>
    </td>
    <td>
        <input type="number" class="form-control form-control-sm border-danger text-danger fw-bold" id="widget-stock-{{ $item->id }}" value="{{ $item->stock }}" min="0">
    </td>
    <td>
        <button class="btn btn-sm btn-success px-3" onclick="updateStock({{ $item->id }}, 'widget-stock-{{ $item->id }}')">Simpan</button>
    </td>
</tr>
@empty
<tr><td colspan="3" class="text-center py-4 text-muted">Semua stok produk aman!</td></tr>
@endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- SEKSI 4: TOP PERFORMANCE & QUICK ACTIONS --}}
    <div class="row g-4">
        
        {{-- Top 5 Produk Terlaris --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-trophy text-warning me-2"></i> Top 5 Produk Terlaris</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Produk</th>
                                    <th class="text-center">Terjual</th>
                                    <th class="text-end pe-4">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $prod)
<tr>
    <td class="ps-4">
        <strong>{{ $prod->product_name }}</strong> <br>
        <small class="text-muted">{{ $prod->variant_name }}</small>
    </td>
    <td class="text-center"><span class="badge bg-primary rounded-pill">{{ $prod->total_sold }} pcs</span></td>
    <td class="text-end pe-4 fw-bold text-success">Rp {{ number_format($prod->total_revenue, 0, ',', '.') }}</td>
</tr>
@empty
<tr><td colspan="3" class="text-center py-4 text-muted">Belum ada data penjualan produk.</td></tr>
@endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pintasan Cepat (Quick Action Bar) --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-lightning-charge text-warning me-1"></i> Pintasan Cepat</h5>
                </div>
                <div class="card-body px-4">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary w-100 py-3 text-start d-flex align-items-center">
                                <i class="bi bi-plus-circle fs-3 me-2"></i>
                                <div><strong class="d-block">Tambah Produk</strong><small class="text-muted fs-8">Input produk baru</small></div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-success w-100 py-3 text-start d-flex align-items-center">
                                <i class="bi bi-bag-check fs-3 me-2"></i>
                                <div><strong class="d-block">Kelola Pesanan</strong><small class="text-muted fs-8">Proses transaksi</small></div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.linktree.index') }}" class="btn btn-outline-info w-100 py-3 text-start d-flex align-items-center">
                                <i class="bi bi-diagram-2 fs-3 me-2"></i>
                                <div><strong class="d-block">Atur Linktree</strong><small class="text-muted fs-8">Update sosmed & bio</small></div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary w-100 py-3 text-start d-flex align-items-center">
                                <i class="bi bi-gear fs-3 me-2"></i>
                                <div><strong class="d-block">Pengaturan Site</strong><small class="text-muted fs-8">Konfigurasi toko</small></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Modal Detail Traffic -->
<div class="modal fade" id="trafficModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Top Halaman Dikunjungi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group" id="trafficList">
            <li class="list-group-item text-center">Memuat data...</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail & Quick Edit Stok -->
<div class="modal fade" id="lowStockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kelola Stok Menipis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Produk & Varian</th>
                    <th width="150">Stok Sekarang</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody id="lowStockTable">
                <tr><td colspan="3" class="text-center">Memuat data...</td></tr>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- SCRIPT INTEGRASI CHART.JS DUAL-AXIS & AJAX --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Filter Handlers
    function setFilter(val) {
        document.getElementById('filterInput').value = val;
        document.getElementById('filterForm').submit();
    }
    function toggleCustomDate() {
        const container = document.getElementById('customDateContainer');
        container.classList.toggle('d-none');
        document.getElementById('filterInput').value = 'custom';
    }
    if ("{{ $filter }}" === "custom") {
        document.getElementById('customDateContainer').classList.remove('d-none');
    }

    // 1. Dual-Axis Line Chart (Traffic vs Penjualan)
    const dualEl = document.getElementById('dualAxisChart');
    const chartData = JSON.parse(dualEl.dataset.chart || '{}');

    new Chart(dualEl.getContext('2d'), {
        type: 'line',
        data: {
            labels: chartData.labels || [],
            datasets: [
                {
                    label: 'Omzet Penjualan (Rp)',
                    data: chartData.sales || [],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    yAxisID: 'ySales',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Pengunjung (Visitors)',
                    data: chartData.visitors || [],
                    borderColor: '#0d6efd',
                    backgroundColor: 'transparent',
                    borderDash: [5, 5],
                    yAxisID: 'yVisitors',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                ySales: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') }
                },
                yVisitors: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });

    // 2. Donut Chart (Sumber Traffic)
    const donutEl = document.getElementById('donutChart');
    const donutData = JSON.parse(donutEl.dataset.donut || '{}');
    new Chart(donutEl.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(donutData).length ? Object.keys(donutData) : ['Direct'],
            datasets: [{
                data: Object.values(donutData).length ? Object.values(donutData) : [1],
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
    });

    // 3. Mini Bar Chart (Halaman Terpopuler)
    const topPagesEl = document.getElementById('topPagesChart');
    const rawTopPages = JSON.parse(topPagesEl.dataset.toppages || '[]');
    new Chart(topPagesEl.getContext('2d'), {
        type: 'bar',
        data: {
            labels: rawTopPages.map(p => p.visited_page),
            datasets: [{
                data: rawTopPages.map(p => p.total),
                backgroundColor: '#0dcaf0'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, indexAxis: 'y' }
    });

    // AJAX Modal & Quick Update (Menggunakan Named Routes Bawaan Laravel)
    function openTrafficModal() {
        new bootstrap.Modal(document.getElementById('trafficModal')).show();
        fetch("{{ route('admin.dashboard.traffic') }}")
            .then(res => res.json())
            .then(data => {
                let html = '';
                if(data.top_pages && data.top_pages.length > 0) {
                    data.top_pages.forEach(p => {
                        html += `<li class="list-group-item d-flex justify-content-between align-items-center"><code>${p.visited_page}</code><span class="badge bg-primary rounded-pill">${p.total} hits</span></li>`;
                    });
                } else { html = '<li class="list-group-item text-center text-muted">Belum ada kunjungan.</li>'; }
                document.getElementById('trafficList').innerHTML = html;
            });
    }

    function openLowStockModal() {
        new bootstrap.Modal(document.getElementById('lowStockModal')).show();
        fetch("{{ route('admin.dashboard.low-stock') }}")
            .then(res => res.json())
            .then(data => {
                let html = '';
                if(data.items && data.items.length > 0) {
                    data.items.forEach(item => {
                        html += `<tr><td><strong>${item.product_name}</strong><br><small class="text-muted">${item.variant_name}</small></td><td><input type="number" class="form-control form-control-sm" id="stock-val-${item.id}" value="${item.stock}" min="0"></td><td><button class="btn btn-sm btn-success" onclick="updateStock(${item.id}, 'stock-val-${item.id}')">Simpan</button></td></tr>`;
                    });
                } else { html = '<tr><td colspan="3" class="text-center text-muted">Semua stok aman!</td></tr>'; }
                document.getElementById('lowStockTable').innerHTML = html;
            });
    }

    function updateStock(variantId, inputId) {
        const inputEl = document.getElementById(inputId);
        if (!inputEl) return;

        const newStock = parseInt(inputEl.value);
        if (isNaN(newStock) || newStock < 0) {
            alert("Harap masukkan stok yang valid!");
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]') 
            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            : '{{ csrf_token() }}';

        fetch("{{ route('admin.dashboard.update-stock') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken 
            },
            body: JSON.stringify({ variant_id: variantId, stock: newStock })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Gagal memperbarui stok.');
            return data;
        })
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(err => {
            console.error("Error Quick Update Stock:", err);
            alert("Gagal memperbarui stok: " + err.message);
        });
    }
</script>

<style>
    .style-cursor { cursor: pointer; }
    .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card-hover:hover { transform: translateY(-3px); }
    .fs-7 { font-size: 0.85rem; }
    .fs-8 { font-size: 0.75rem; }
</style>
@endsection