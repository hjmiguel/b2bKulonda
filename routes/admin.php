<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SequenceManagementController;
use App\Http\Controllers\Admin\AGTLogsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| 
| Estas rotas requerem autenticação e permissões de administrador.
| Todas as rotas estão protegidas pelo middleware 'admin'.
|
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/sales', [ReportsController::class, 'sales'])->name('sales');
        Route::get('/taxes', [ReportsController::class, 'taxes'])->name('taxes');
        Route::get('/customers', [ReportsController::class, 'customers'])->name('customers');
        Route::get('/agt-submissions', [ReportsController::class, 'agtSubmissions'])->name('agt-submissions');
        Route::get('/sequences', [ReportsController::class, 'sequences'])->name('sequences');
    });
    
    // Sequence Management
    Route::prefix('sequences')->name('sequences.')->group(function () {
        Route::get('/', [SequenceManagementController::class, 'index'])->name('index');
        Route::get('/create', [SequenceManagementController::class, 'create'])->name('create');
        Route::post('/', [SequenceManagementController::class, 'store'])->name('store');
        Route::get('/{sequence}', [SequenceManagementController::class, 'show'])->name('show');
        Route::post('/{sequence}/reset', [SequenceManagementController::class, 'reset'])->name('reset');
        Route::post('/initialize-year', [SequenceManagementController::class, 'initializeYear'])->name('initialize-year');
        Route::get('/{sequence}/verify', [SequenceManagementController::class, 'verify'])->name('verify');
    });
    
    // AGT Integration Logs
    Route::prefix('agt')->name('agt.')->group(function () {
        Route::get('/logs', [AGTLogsController::class, 'index'])->name('logs');
        Route::get('/logs/{document}', [AGTLogsController::class, 'show'])->name('logs.show');
        Route::post('/logs/{document}/retry', [AGTLogsController::class, 'retry'])->name('logs.retry');
        Route::post('/logs/bulk-retry', [AGTLogsController::class, 'bulkRetry'])->name('logs.bulk-retry');
        Route::get('/status', [AGTLogsController::class, 'status'])->name('status');
        Route::post('/clear-failed-jobs', [AGTLogsController::class, 'clearFailedJobs'])->name('clear-failed-jobs');
        Route::get('/export', [AGTLogsController::class, 'export'])->name('export');
    });
});
