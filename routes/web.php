<?php

use App\Http\Controllers\Auth\PreviewSessionController;
use App\Http\Controllers\HomeCalendarController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::post('preview-login', [PreviewSessionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('preview.login');

Route::post('preview-logout', [PreviewSessionController::class, 'destroy'])
    ->name('preview.logout');

Route::middleware('panel.access')->group(function () {
    Route::get('home', [HomeCalendarController::class, 'index'])->name('panel.home');
    Route::post('home/reminders', [HomeCalendarController::class, 'store'])->name('panel.home.reminders.store');
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('job-orders', 'admin.job-orders.index')->name('job-orders.index');
    Route::view('inventory', 'admin.inventory.index')->name('inventory.index');

    Route::prefix('subscriptions')->group(function () {
        Route::get('add', [SubscriberController::class, 'create'])->name('subscribers.create');
        Route::get('account-number', [SubscriberController::class, 'generateAccountNumber'])->name('subscribers.account-number');
        Route::post('records', [SubscriberController::class, 'store'])->name('subscribers.store');
        Route::get('records', [SubscriberController::class, 'index'])->name('subscribers.index');
        Route::post('records/{subscriber}/comments', [SubscriberController::class, 'storeComment'])
            ->whereNumber('subscriber')
            ->name('subscribers.comments.store');
        Route::patch('records/{subscriber}/personal', [SubscriberController::class, 'updatePersonalDetails'])
            ->whereNumber('subscriber')
            ->name('subscribers.personal.update');
        Route::patch('records/{subscriber}/service', [SubscriberController::class, 'updateServiceDetails'])
            ->whereNumber('subscriber')
            ->name('subscribers.service.update');
        Route::patch('records/{subscriber}/mikrotik', [SubscriberController::class, 'updateMikrotikDetails'])
            ->whereNumber('subscriber')
            ->name('subscribers.mikrotik.update');
        Route::get('records/{subscriber}', [SubscriberController::class, 'show'])
            ->whereNumber('subscriber')
            ->name('subscribers.details');
        Route::get('products', [PlanController::class, 'index'])->name('products.index');
        Route::post('products', [PlanController::class, 'store'])->name('products.store');
        Route::patch('products/{plan}', [PlanController::class, 'update'])->name('products.update');
        Route::delete('products/{plan}', [PlanController::class, 'destroy'])->name('products.destroy');
        Route::view('policies', 'admin.policies.index')->name('policies.index');
    });

    Route::prefix('reports')->group(function () {
        Route::view('/', 'admin.reports.index')->name('reports.index');
        Route::get('daily', fn () => redirect()->to(route('reports.index').'#daily'))->name('reports.daily');
        Route::get('transactions', fn () => redirect()->to(route('reports.index').'#transactions'))->name('reports.transactions');
        Route::get('expenses', fn () => redirect()->to(route('reports.index').'#expenses'))->name('reports.expenses');
    });

    Route::prefix('integrations')->group(function () {
        Route::view('messaging', 'admin.integrations.messaging')->name('integrations.messaging');
        Route::view('scheduler', 'admin.integrations.scheduler')->name('integrations.scheduler');
        Route::view('settings', 'admin.integrations.settings')->name('integrations.settings');
    });

    Route::prefix('panel-settings')->group(function () {
        Route::get('/', fn () => to_route('products.index'))->name('panel.settings');
        Route::view('router', 'admin.settings.router.index')->name('router.index');
        Route::view('router/backups', 'admin.settings.router.backups')->name('router.backups');
        Route::view('staff', 'admin.settings.users.index')->name('staff.index');
    });

    Route::prefix('logs')->group(function () {
        Route::get('/', fn () => to_route('logs.system'))->name('logs.index');
        Route::view('system', 'admin.logs.system')->name('logs.system');
        Route::view('activity', 'admin.logs.activity')->name('logs.activity');
    });
});

require __DIR__.'/settings.php';
