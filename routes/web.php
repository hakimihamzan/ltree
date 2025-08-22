<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ApprovalChainController;
use App\Http\Controllers\PurchaseRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User management
    Route::resource('users', UserController::class);

    // Department management
    Route::resource('departments', DepartmentController::class);

    // Approval chain management
    Route::resource('approval_chains', ApprovalChainController::class);
    Route::get('/approval_chains_parents/{department_id}', [ApprovalChainController::class, 'getParentChains'])->name('approval_chains.parents');

    // Purchase request management
    Route::resource('purchase_requests', PurchaseRequestController::class);
    Route::get('/purchase_requests_pending', [PurchaseRequestController::class, 'pending'])->name('purchase_requests.pending');
    Route::post('/purchase_requests/{purchase_request}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase_requests.approve');
    Route::post('/purchase_requests/{purchase_request}/reject', [PurchaseRequestController::class, 'reject'])->name('purchase_requests.reject');
});

require __DIR__.'/auth.php';
