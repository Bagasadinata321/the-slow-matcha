<?php

use Illuminate\Support\Facades\Route;

// Import Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LinktreeController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ReviewController;

// Import Public Storefront Controllers
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProductController as PublicProductController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\Public\LinktreeController as PublicLinktreeController;
use App\Http\Controllers\Public\ReviewController as PublicReviewController;

// Import Public Customer Area Controllers
use App\Http\Controllers\Public\Account\OrderHistoryController;
use App\Http\Controllers\Public\Account\AddressController;
use App\Http\Controllers\Public\Account\ProfileController as AccountProfileController;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC STOREFRONT ROUTES
|--------------------------------------------------------------------------
*/

// HUBUNGKAN ROUTE METODE INDEX KE PUBLIC HOMECONTROLLER
Route::get('/', [\App\Http\Controllers\Public\HomeController::class, 'index'])->name('public.home');
Route::get('/about', [HomeController::class, 'about'])->name('public.about');

// Produk Storefront
Route::get('/products', [PublicProductController::class, 'index'])->name('public.products.index');
Route::get('/product/{slug}', [PublicProductController::class, 'show'])->name('public.products.show');

// Cart Routes (/cart)
Route::prefix('cart')->name('public.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'store'])->name('store');
    Route::put('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'destroy'])->name('destroy');
});

// Checkout Routes (/checkout)
Route::prefix('checkout')->name('public.checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{invoice_number}', [CheckoutController::class, 'success'])->name('success');
});

// Lacak Pesanan Public (/track)
Route::get('/track', [OrderTrackingController::class, 'index'])->name('public.orders.track');

// Public Linktree (/tree)
Route::get('/linktree', [PublicLinktreeController::class, 'index'])->name('linktree.public');


/*
|--------------------------------------------------------------------------
| 2. CUSTOMER AREA ROUTES (AUTHENTICATED USER)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('account')->name('public.account.')->group(function () {
    // 2.1 Riwayat Pesanan (/account/orders)
    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/orders/{invoice_number}', [OrderHistoryController::class, 'show'])->name('orders.show');
    Route::post('/orders/{invoice_number}/reorder', [OrderHistoryController::class, 'reorder'])->name('orders.reorder');
    
    // Fitur Tambahan: Submit Rating & Ulasan Produk per Pesanan
    Route::post('/orders/{invoice_number}/reviews', [PublicReviewController::class, 'store'])->name('orders.reviews.store');

    // 2.2 Buku Alamat (/account/addresses)
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{id}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{id}/primary', [AddressController::class, 'setPrimary'])->name('addresses.primary');

    // 2.3 Pengaturan Akun (/account/profile)
    Route::get('/profile', [AccountProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AccountProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [AccountProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// User Default Dashboard (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| 3. ADMIN PANEL ROUTES (AUTHENTICATED ADMIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        // Dashboard & Quick Modals
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/traffic-stats', [DashboardController::class, 'trafficStats'])->name('dashboard.traffic');
        Route::get('/dashboard/low-stock-items', [DashboardController::class, 'lowStockItems'])->name('dashboard.low-stock');
        Route::post('/dashboard/quick-update-stock', [DashboardController::class, 'quickUpdateStock'])->name('dashboard.update-stock');

        // Modul Produk
        Route::resource('products', ProductController::class);
        Route::prefix('products')->name('products.')->group(function () {
            Route::delete('/media/{id}', [ProductController::class, 'deleteMedia'])->name('media.destroy');
            Route::post('/upload-editor-image', [ProductController::class, 'uploadEditorImage'])->name('upload-editor-image');
        });
        // Modul Ulasan / Rating Admin (TAMBAHKAN DI SINI)
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::patch('/{review}/reply', [ReviewController::class, 'reply'])->name('reply');
            Route::patch('/{review}/toggle', [ReviewController::class, 'toggleApproval'])->name('toggle');
        });

        // Modul Pesanan
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::patch('/{review}/reply', [ReviewController::class, 'reply'])->name('reply');
            Route::patch('/{review}/toggle', [ReviewController::class, 'toggleStatus'])->name('toggle');
        });

        // Modul Pelanggan
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/export', [CustomerController::class, 'exportCsv'])->name('customers.export');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
        Route::patch('/customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

        // Modul Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::put('/update', [SettingController::class, 'updateSite'])->name('update');
        });

        // Modul Linktree Builder
        Route::prefix('linktree')->name('linktree.')->group(function () {
            Route::get('/', [LinktreeController::class, 'index'])->name('index');
            Route::post('/store', [LinktreeController::class, 'store'])->name('store');
            Route::put('/update-all', [LinktreeController::class, 'updateAll'])->name('update-all');
            Route::post('/reorder', [LinktreeController::class, 'reorder'])->name('reorder');
            Route::delete('/{id}', [LinktreeController::class, 'destroy'])->name('destroy');
        });
    });

require __DIR__ . '/auth.php';