<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\ITTicketController;

// Redirect root to login
Route::get('/', fn() => redirect()->route('login'));

// Home redirect to dashboard
Route::get('/home', [LoginController::class, 'showLoginForm'])->middleware('auth');

// Keep-alive route
Route::get('/keep-alive', fn() => response()->json(['status' => 'alive']));



// Auth routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    
    Route::post('/logout-all-staff', function () {
        $staffIds = \App\Models\User::where('role', 'staff')->pluck('id');
        
        if (config('session.driver') === 'database') {
            \Illuminate\Support\Facades\DB::table('sessions')->whereIn('user_id', $staffIds)->delete();
            return back()->with('success', 'Sabhi staff members logout ho gaye hain.');
        } else {
            // Agar abhi tak database session active nahi hai toh poori session directory clear karni padegi
            $files = glob(storage_path('framework/sessions/*'));
            foreach($files as $file){
                if(is_file($file) && basename($file) !== '.gitignore') {
                    unlink($file);
                }
            }
            return redirect()->route('login')->with('success', 'Sabhi sessions clear kar diye gaye hain (Admin bhi logout ho gaya).');
        }
    })->name('logout-all-staff');
});

// Manager routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', fn() => view('manager.dashboard'))->name('dashboard');
});

// Staff routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/track-task', [StaffDashboardController::class, 'trackTask'])->name('track-task');
    Route::get('/guide', [StaffDashboardController::class, 'guide'])->name('guide');
    Route::post('/profile-update-request', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'store'])->name('profile.update.request');
});

// All Staff (including IT, Admin, Manager) can access Daily Backup
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/daily-backup', [\App\Http\Controllers\SystemBackupController::class, 'index'])->name('daily-backup.index');
    Route::get('/daily-backup/create', [\App\Http\Controllers\SystemBackupController::class, 'create'])->name('daily-backup.create');
    Route::post('/daily-backup', [\App\Http\Controllers\SystemBackupController::class, 'store'])->name('daily-backup.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/change-password', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'changePassword'])->name('password.update');
    
    // Profile Update Requests for Admin/Manager
    Route::get('/profile-requests', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'index'])->name('profile.requests.index');
    Route::post('/profile-requests/{id}/approve', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'approve'])->name('profile.requests.approve');
    Route::post('/profile-requests/{id}/reject', [\App\Http\Controllers\ProfileUpdateRequestController::class, 'reject'])->name('profile.requests.reject');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('read-all');
    });
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
    Route::get('/backup-locations', [\App\Http\Controllers\ITManagementController::class, 'backupLocationsIndex'])->name('backup-locations.index');
    Route::post('/backup-locations', [\App\Http\Controllers\ITManagementController::class, 'backupLocationsStore'])->name('backup-locations.store');
    Route::delete('/backup-locations/{id}', [\App\Http\Controllers\ITManagementController::class, 'backupLocationsDestroy'])->name('backup-locations.destroy');

    Route::get('/backup-reports', [\App\Http\Controllers\ITManagementController::class, 'backupReportsIndex'])->name('backup-reports.index');
    Route::get('/backup-reports/export/pdf', [\App\Http\Controllers\ITManagementController::class, 'backupReportsExportPdf'])->name('backup-reports.export-pdf');
    Route::get('/backup-reports/export/excel', [\App\Http\Controllers\ITManagementController::class, 'backupReportsExportExcel'])->name('backup-reports.export-excel');

    Route::get('/backup-defaulters', [\App\Http\Controllers\ITManagementController::class, 'defaultersIndex'])->name('backup-defaulters.index');
    Route::post('/backup-defaulters/{id}/send-mail', [\App\Http\Controllers\ITManagementController::class, 'sendDefaulterMail'])->name('backup-defaulters.send-mail');
    Route::post('/backup-defaulters/send-bulk-mail', [\App\Http\Controllers\ITManagementController::class, 'sendBulkDefaulterMail'])->name('backup-defaulters.send-bulk-mail');
    Route::get('/backup-defaulters/mail-logs', [\App\Http\Controllers\ITManagementController::class, 'defaulterMailLogsIndex'])->name('backup-defaulters.mail-logs');
    Route::get('/backup-defaulters/export/pdf', [\App\Http\Controllers\ITManagementController::class, 'defaultersExportPdf'])->name('backup-defaulters.export-pdf');
    Route::get('/backup-defaulters/export/excel', [\App\Http\Controllers\ITManagementController::class, 'defaultersExportExcel'])->name('backup-defaulters.export-excel');
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
    Route::put('/purchases/{id}', [\App\Http\Controllers\StockManagementController::class, 'purchaseUpdate'])->name('purchases.update');
    Route::delete('/purchases/{id}', [\App\Http\Controllers\StockManagementController::class, 'purchaseDestroy'])->name('purchases.destroy');
    
    Route::get('/allotments', [\App\Http\Controllers\StockManagementController::class, 'allotmentIndex'])->name('allotments.index');
    Route::post('/allotments', [\App\Http\Controllers\StockManagementController::class, 'allotmentStore'])->name('allotments.store');
    Route::get('/allotments/history', [\App\Http\Controllers\StockManagementController::class, 'allotmentHistoryIndex'])->name('allotments.history');
    Route::get('/allotments/export/pdf', [\App\Http\Controllers\StockManagementController::class, 'allotmentExportPdf'])->name('allotments.export-pdf');
    Route::get('/allotments/export/excel', [\App\Http\Controllers\StockManagementController::class, 'allotmentExportExcel'])->name('allotments.export-excel');
    Route::post('/allotments/{id}/return', [\App\Http\Controllers\StockManagementController::class, 'allotmentReturn'])->name('allotments.return');
    Route::put('/allotments/{id}', [\App\Http\Controllers\StockManagementController::class, 'allotmentUpdate'])->name('allotments.update');
    Route::delete('/allotments/{id}', [\App\Http\Controllers\StockManagementController::class, 'allotmentDestroy'])->name('allotments.destroy');
});

// Daily Report (All authenticated users)
Route::middleware('auth')->prefix('daily-report')->name('daily-report.')->group(function () {
    Route::get('/',          [DailyReportController::class, 'index'])->name('index');
    Route::get('/live-tasks', [DailyReportController::class, 'liveTasks'])->name('live-tasks');
    Route::post('/task/start', [DailyReportController::class, 'startTask'])->name('task.start');
    Route::post('/task/other', [DailyReportController::class, 'addOtherTask'])->name('task.other');
    Route::post('/task/{task}/end', [DailyReportController::class, 'endTask'])->name('task.end');
    Route::post('/task/{task}/pause', [DailyReportController::class, 'pauseTask'])->name('task.pause');
    Route::post('/task/{task}/resume', [DailyReportController::class, 'resumeTask'])->name('task.resume');
    Route::post('/task/{task}/update-desc', [DailyReportController::class, 'updateTaskDescription'])->name('task.update-desc');
    Route::get('/task/{task}/history', [DailyReportController::class, 'getTaskHistory'])->name('task.history');
    Route::get('/task/{task}/report', [DailyReportController::class, 'taskReport'])->name('task.report');
    Route::get('/task/{task}/export/{format}', [DailyReportController::class, 'exportTaskReport'])->name('task.export');
    
    Route::get('/export',    [DailyReportController::class, 'export'])->name('export');

    Route::post('/assign-task', [DailyReportController::class, 'assignTask'])->name('assign-task');
    Route::get('/task/{task}/comments', [\App\Http\Controllers\TaskCommentController::class, 'index'])->name('task.comments.index');
    Route::post('/task/{task}/comments', [\App\Http\Controllers\TaskCommentController::class, 'store'])->name('task.comments.store');

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
    Route::get('/report', [ITTicketController::class, 'report'])->name('report');
    Route::get('/report/export', [ITTicketController::class, 'exportReport'])->name('report.export');
    Route::get('/{itTicket}', [ITTicketController::class, 'show'])->name('show');
    Route::post('/{itTicket}/reply', [ITTicketController::class, 'reply'])->name('reply');
    Route::post('/{itTicket}/status', [ITTicketController::class, 'updateStatus'])->name('update-status');
    Route::post('/{itTicket}/assign-time', [ITTicketController::class, 'assignTime'])->name('assign-time');
});
