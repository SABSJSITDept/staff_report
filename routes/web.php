<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\ITTicketController;

// Redirect root to login
Route::get('/', fn() => redirect()->route('login'));

// Auth routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
});

// Manager routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', fn() => view('manager.dashboard'))->name('dashboard');
});

// Staff routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/guide', [StaffDashboardController::class, 'guide'])->name('guide');
    Route::post('/profile-update-request', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'store'])->name('profile.update.request');
});

Route::middleware('auth')->group(function () {
    Route::post('/change-password', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'changePassword'])->name('password.update');
    
    // Profile Update Requests for Admin/Manager
    Route::get('/profile-requests', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'index'])->name('profile.requests.index');
    Route::post('/profile-requests/{id}/approve', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'approve'])->name('profile.requests.approve');
    Route::post('/profile-requests/{id}/reject', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'reject'])->name('profile.requests.reject');
});

// Office Management (Admin only)
Route::middleware(['auth', 'role:admin'])->prefix('office')->name('office.')->group(function () {
    Route::get('/create', fn() => view('Office.OfficeCreate'))->name('create');
    Route::get('/view', fn() => view('Office.ViewOffice'))->name('view');
});

// Department Management (Admin only)
Route::middleware(['auth', 'role:admin'])->prefix('department')->name('department.')->group(function () {
    Route::get('/create', fn() => view('Department.DepartmentCreate'))->name('create');
    Route::get('/view', fn() => view('Department.DepartmentView'))->name('view');
});

// Staff Management (Admin only)
Route::middleware(['auth', 'role:admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/create', fn() => view('Staff.StaffCreate'))->name('create');
    Route::get('/view', fn() => view('Staff.ViewStaff'))->name('view');
});

// IT Management (IT Dept only)
Route::middleware(['auth', 'role:IT DEPARTMENT'])->prefix('it-management')->name('it-management.')->group(function () {
    Route::get('/allotment', [\App\Http\Controllers\ITManagementController::class, 'allotmentIndex'])->name('allotment.index');
    Route::post('/allotment', [\App\Http\Controllers\ITManagementController::class, 'allotmentStore'])->name('allotment.store');
    
    Route::get('/backup', [\App\Http\Controllers\ITManagementController::class, 'backupIndex'])->name('backup.index');
    Route::post('/backup', [\App\Http\Controllers\ITManagementController::class, 'backupStore'])->name('backup.store');
});

// Stock Management (IT Dept only)
Route::middleware(['auth', 'role:IT DEPARTMENT'])->prefix('stock-management')->name('stock-management.')->group(function () {
    Route::get('/categories', [\App\Http\Controllers\StockManagementController::class, 'categoryIndex'])->name('categories.index');
    Route::post('/categories', [\App\Http\Controllers\StockManagementController::class, 'categoryStore'])->name('categories.store');
    
    Route::get('/items', [\App\Http\Controllers\StockManagementController::class, 'itemIndex'])->name('items.index');
    Route::post('/items', [\App\Http\Controllers\StockManagementController::class, 'itemStore'])->name('items.store');
    Route::put('/items/{id}', [\App\Http\Controllers\StockManagementController::class, 'itemUpdate'])->name('items.update');
    Route::delete('/items/{id}', [\App\Http\Controllers\StockManagementController::class, 'itemDestroy'])->name('items.destroy');
    
    Route::post('/items/brands', [\App\Http\Controllers\StockManagementController::class, 'brandStore'])->name('items.brands.store');
    Route::put('/items/brands/{id}', [\App\Http\Controllers\StockManagementController::class, 'brandUpdate'])->name('items.brands.update');
    Route::delete('/items/brands/{id}', [\App\Http\Controllers\StockManagementController::class, 'brandDestroy'])->name('items.brands.destroy');
    
    Route::get('/purchases', [\App\Http\Controllers\StockManagementController::class, 'purchaseIndex'])->name('purchases.index');
    Route::post('/purchases', [\App\Http\Controllers\StockManagementController::class, 'purchaseStore'])->name('purchases.store');
    
    Route::get('/allotments', [\App\Http\Controllers\StockManagementController::class, 'allotmentIndex'])->name('allotments.index');
    Route::post('/allotments', [\App\Http\Controllers\StockManagementController::class, 'allotmentStore'])->name('allotments.store');
});

// Daily Report (All authenticated users)
Route::middleware('auth')->prefix('daily-report')->name('daily-report.')->group(function () {
    Route::get('/',          [DailyReportController::class, 'index'])->name('index');
    Route::get('/export',    [DailyReportController::class, 'export'])->name('export');
    Route::get('/create',   [DailyReportController::class, 'create'])->name('create');
    Route::post('/',        [DailyReportController::class, 'store'])->name('store');
    Route::get('/{dailyReport}',        [DailyReportController::class, 'show'])->name('show');
    Route::get('/{dailyReport}/edit',   [DailyReportController::class, 'edit'])->name('edit');
    Route::put('/{dailyReport}',        [DailyReportController::class, 'update'])->name('update');
    Route::delete('/{dailyReport}',     [DailyReportController::class, 'destroy'])->name('destroy');
    Route::get('/last-tasks',           [DailyReportController::class, 'getLastTasks'])->name('last-tasks');
});

// IT Troubleshooting Ticket System
Route::middleware('auth')->prefix('it-tickets')->name('it-tickets.')->group(function () {
    Route::get('/', [ITTicketController::class, 'index'])->name('index');
    Route::get('/create', [ITTicketController::class, 'create'])->name('create');
    Route::post('/', [ITTicketController::class, 'store'])->name('store');
    Route::get('/{itTicket}', [ITTicketController::class, 'show'])->name('show');
    Route::post('/{itTicket}/reply', [ITTicketController::class, 'reply'])->name('reply');
    Route::post('/{itTicket}/status', [ITTicketController::class, 'updateStatus'])->name('update-status');
    Route::post('/{itTicket}/assign-time', [ITTicketController::class, 'assignTime'])->name('assign-time');
});
