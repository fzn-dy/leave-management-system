<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LeaveRequestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Public Routes
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/me', function (Request $request) {
        return $request->user();
    })->name('me');

    // --- FITUR ADMIN ---
    Route::prefix('admin')->group(function () {
        // Kelola User
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        
        // Kelola Request (Approve/Reject)
        Route::get('/leave-requests', [LeaveRequestController::class, 'adminIndex'])->name('admin.leave-requests.index');
        Route::post('/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::post('/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    });

    // --- FITUR USER (Karyawan) ---
    Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index'); 
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store'); 
    Route::get('/leave-balances', [LeaveRequestController::class, 'getBalances'])->name('leave-balances.index'); 
    Route::post('/leave-requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');

    // --- SHARED DELETE ---
    Route::delete('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->name('leave-requests.destroy');
});