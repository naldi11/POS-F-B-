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

// --- Customer Routes ---
Volt::route('/', 'customer.welcome')->name('welcome');

// Menu Catalog & Cart
Route::get('/menu', MenuList::class)->name('customer.menu');
Route::get('/cart', Cart::class)->name('customer.cart');
Route::get('/checkout', Checkout::class)->name('customer.checkout');
Route::get('/order/{id}', OrderStatus::class)->name('customer.order-status');
Route::get('/order/{id}/print', \App\Http\Controllers\OrderPrintController::class)->name('order.print');

// --- Admin/Staff Routes ---
Route::get('dashboard', Overview::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


use App\Livewire\Staff\CashierDashboard;
use App\Livewire\Marketing\Dashboard as MarketingDashboard;
use App\Livewire\Marketing\PromotionManager;

use App\Livewire\Admin\Report;

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/categories', CategoryManager::class)->name('admin.categories');
    Route::get('/admin/menus', MenuManager::class)->name('admin.menus');
    Route::get('/admin/qrcode', \App\Livewire\Admin\TableManager::class)->name('admin.qrcode');
    Route::get('/admin/payments', \App\Livewire\Admin\PaymentManager::class)->name('admin.payments');
    Route::get('/admin/receipt-settings', \App\Livewire\Admin\ReceiptSettings::class)->name('admin.receipt-settings');
    Route::get('/admin/reports', Report::class)->name('admin.reports');

    // Staff Routes
    Route::get('/staff/cashier', CashierDashboard::class)->name('staff.cashier');

    // Marketing Routes
    Route::get('/marketing/dashboard', MarketingDashboard::class)->name('marketing.dashboard');
    Route::get('/marketing/promotions', PromotionManager::class)->name('marketing.promotions');
});

require __DIR__.'/auth.php';
