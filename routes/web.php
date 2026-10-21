<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketNotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/about', 'about')->name('about');
Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/language', [ProfileController::class, 'updateLocale'])->name('settings.language');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/notifications/{notificationId}/open', [TicketNotificationController::class, 'open'])
        ->name('notifications.open');
    Route::post('/notifications/read-all', [TicketNotificationController::class, 'markAllRead'])
        ->name('notifications.read-all');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('can:manage-records')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('technicians', TechnicianController::class)->except(['index', 'show']);
        Route::post('/technicians/{technician}/toggle-status', [TechnicianController::class, 'toggleStatus'])
            ->name('technicians.toggle-status');
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
        Route::post('/tickets/{ticket}/comments', [TicketController::class, 'comment'])
            ->name('tickets.comments.store');
    });

    Route::middleware('can:create-tickets')->group(function () {
        Route::resource('tickets', TicketController::class)->only(['create', 'store']);
    });

    Route::middleware('can:manage-tickets')->group(function () {
        Route::resource('tickets', TicketController::class)->only(['edit', 'update', 'destroy']);
    });

    Route::get('/technicians', [TechnicianController::class, 'index'])->name('technicians.index');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    Route::get('/tickets/{ticket}/print', [TicketController::class, 'print'])->name('tickets.print');
    Route::get('/tickets/{ticket}/attachment', [TicketController::class, 'downloadAttachment'])
        ->name('tickets.attachment');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [ProfileController::class, 'update'])->name('settings.update');
    Route::post('/settings/theme', [ProfileController::class, 'updateTheme'])->name('settings.theme');
});
