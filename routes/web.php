<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DetectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenyakitController;

// ======================
// PUBLIC ROUTES (Tidak perlu login)
// ======================
Route::get('/', [AuthController::class, 'landingPage'])
     ->name('landing');

// ======================
// AUTH ROUTES
// ======================
Route::get('/login', [AuthController::class, 'loginPage'])
     ->name('login')
     ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
     ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout');

// ======================
// PROTECTED ROUTES (Harus Login)
// ======================

Route::middleware(['auth', 'role:all'])->group(function () {
     Route::get('/dashboard', [DetectionController::class, 'dashboard'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin,operator,manager'])->group(function () {
     Route::get('/riwayat', [DetectionController::class, 'riwayat'])->name('riwayat');
     Route::get('/riwayat/export', [DetectionController::class, 'exportCsv'])->name('riwayat.export');
     Route::get('/detect/{id}/pdf', [DetectionController::class, 'downloadPdf'])->name('detect.pdf');
     Route::get('/detect/{id}/detail', [DetectionController::class, 'show'])->name('detect.show');
});

Route::middleware(['auth', 'role:admin,operator'])->group(function () {
     // Route::get('/dashboard', [DetectionController::class, 'dashboard'])->name('dashboard');

     Route::get('/deteksi', [DetectionController::class, 'deteksi'])->name('deteksi');
     Route::post('/predict', [DetectionController::class, 'predict'])->name('predict');
     Route::post('/detect', [DetectionController::class, 'store'])->name('detect.store');
     Route::delete('/detect/{id}', [DetectionController::class, 'destroy'])->name('detect.destroy');
     Route::delete('/detect-all', [DetectionController::class, 'destroyAll'])->name('detect.destroyAll');

     // Route::get('/riwayat', [DetectionController::class, 'riwayat'])->name('riwayat');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
     Route::get('/user', [UserController::class, 'index'])->name('user');
     Route::get('/users', [UserController::class, 'index'])->name('users.index');
     Route::post('/users', [UserController::class, 'store'])->name('users.store');
     Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

     Route::get('/penyakit', [PenyakitController::class, 'index'])->name('penyakit');
     Route::get('/penyakits', [PenyakitController::class, 'index'])->name('penyakits.index');
     Route::post('/penyakits', [PenyakitController::class, 'store'])->name('penyakits.store');
     Route::put('/penyakits/{id}', [PenyakitController::class, 'update'])->name('penyakits.update');
     Route::delete('/penyakits/{id}', [PenyakitController::class, 'destroy'])->name('penyakits.destroy');
});

// Route::middleware('auth')->group(function () {

//     // Semua yang login bisa akses dashboard awal
//     Route::get('/dashboard', [DetectionController::class, 'dashboard'])
//          ->name('dashboard');

//     // Manager + Operator + Admin : Boleh lihat riwayat dan detail laporan
//     Route::middleware('can_view_reports')->group(function () {
//         Route::get('/riwayat', [DetectionController::class, 'riwayat'])->name('riwayat');
//         Route::get('/detect/{id}/detail', [DetectionController::class, 'show'])->name('detect.show');
//         Route::get('/detect/{id}/pdf', [DetectionController::class, 'downloadPdf'])->name('detect.pdf');
//     });

//     // Hanya Operator + Admin : Boleh melakukan deteksi baru
//     Route::middleware('can_modify_detection')->group(function () {
//         Route::get('/deteksi', [DetectionController::class, 'deteksi'])->name('deteksi');
//         Route::post('/detect', [DetectionController::class, 'store'])->name('detect.store');
//     });

//     // ======================
//     // ADMIN ONLY
//     // ======================
//     Route::middleware('admin')->group(function () {
//         Route::delete('/detect/{id}', [DetectionController::class, 'destroy'])->name('detect.destroy');
//         Route::delete('/detect-all', [DetectionController::class, 'destroyAll'])->name('detect.destroyAll');
//         Route::get('/riwayat/export', [DetectionController::class, 'exportCsv'])->name('riwayat.export');

//         Route::get('/users', [UserController::class, 'index'])->name('users.index');
//         Route::post('/users', [UserController::class, 'store'])->name('users.store');
//         Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

//         Route::get('/penyakit', [PenyakitController::class, 'index'])->name('penyakit.index');
//         Route::post('/penyakit', [PenyakitController::class, 'store'])->name('penyakit.store');
//         Route::put('/penyakit/{id}', [PenyakitController::class, 'update'])->name('penyakit.update');
//         Route::delete('/penyakit/{id}', [PenyakitController::class, 'destroy'])->name('penyakit.destroy');
//     });
// });
