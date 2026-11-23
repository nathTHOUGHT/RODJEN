<?php

use App\Http\Controllers\Auth\PreviewSessionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::post('preview-login', [PreviewSessionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('preview.login');

Route::post('preview-logout', [PreviewSessionController::class, 'destroy'])
    ->name('preview.logout');

Route::middleware('panel.access')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
