<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\MenuManager;
use App\Livewire\Admin\Overview;
use App\Livewire\Customer\MenuList;
use App\Livewire\Customer\Cart;
use App\Livewire\Customer\Checkout;
use App\Livewire\Customer\OrderStatus;
use App\Livewire\Staff\CashierDashboard;
use App\Livewire\Marketing\Dashboard as MarketingDashboard;
use App\Livewire\Admin\Report;

// =============================================================
// CUSTOMER ROUTES - rumpocafe.site (domain utama)
// =============================================================
Volt::route('/', 'customer.welcome')->name('welcome');
Route::get('/menu', MenuList::class)->name('customer.menu');
Route::get('/cart', Cart::class)->name('customer.cart');
Route::get('/checkout', Checkout::class)->name('customer.checkout');
Route::get('/order/{id}', OrderStatus::class)->name('customer.order-status');
Route::get('/order/{id}/print', \App\Http\Controllers\OrderPrintController::class)->name('order.print');

// =============================================================
// ADMIN / STAFF ROUTES - login.rumpocafe.site (subdomain)
// =============================================================
Route::domain(env('ADMIN_DOMAIN', 'login.rumpocafe.site'))->group(function () {

    // Halaman utama subdomain redirect ke dashboard atau login
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard
    Route::get('/dashboard', Overview::class)
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::view('/profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');

    // Cashier Routes
    Route::middleware(['auth', 'role:cashier'])->group(function () {
        Route::get('/staff/cashier', CashierDashboard::class)->name('staff.cashier');
    });

    // Admin / Management Routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/admin/categories', CategoryManager::class)->name('admin.categories');
        Route::get('/admin/menus', MenuManager::class)->name('admin.menus');
        Route::get('/admin/qrcode', \App\Livewire\Admin\TableManager::class)->name('admin.qrcode');
        Route::get('/admin/payments', \App\Livewire\Admin\PaymentManager::class)->name('admin.payments');
        Route::get('/admin/receipt-settings', \App\Livewire\Admin\ReceiptSettings::class)->name('admin.receipt-settings');
        Route::get('/admin/reports', Report::class)->name('admin.reports');

        Route::get('/marketing/dashboard', MarketingDashboard::class)->name('marketing.dashboard');
        Route::get('/marketing/promotions', \App\Livewire\Marketing\PromotionManager::class)->name('marketing.promotions');
        Route::get('/marketing/bundles', \App\Livewire\Marketing\BundleManager::class)->name('marketing.bundles');
        Route::get('/marketing/events', \App\Livewire\Marketing\EventManager::class)->name('marketing.events');
    });

    // Auth routes (login, register, forgot password, dsb)
    require __DIR__.'/auth.php';
});
