<?php

use App\Http\Controllers\Auth\PreviewSessionController;
use App\Http\Controllers\HomeCalendarController;
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

    Route::prefix('subscriptions')->group(function () {
        $sampleSubscriber = require resource_path('data/sample-subscriber.php');
        $subscriberPreview = ['sampleSubscriber' => $sampleSubscriber];

        Route::view('add', 'admin.subscribers.create')->name('subscribers.create');
        Route::view('records', 'admin.subscribers.index', $subscriberPreview)->name('subscribers.index');
        Route::view('records/details', 'admin.subscribers.show', $subscriberPreview)->name('subscribers.details');
        Route::view('products', 'admin.products.index')->name('products.index');
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
        Route::view('router', 'admin.settings.router.index')->name('router.index');
        Route::view('router/backups', 'admin.settings.router.backups')->name('router.backups');
        Route::view('staff', 'admin.settings.users.index')->name('staff.index');
    });

    Route::prefix('logs')->group(function () {
        Route::view('system', 'admin.logs.system')->name('logs.system');
        Route::view('activity', 'admin.logs.activity')->name('logs.activity');
    });
});

require __DIR__.'/settings.php';
