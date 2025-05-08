<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\dashboardcontroller;
use App\Http\Controllers\stuntingcontroller;

Route::get('/', function () {
    return view('Landing');
});

// Admin Auth
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin dashboard
Route::get('/admin/dashboard', [dashboardcontroller::class, 'index'])->name('admin.dashboard');

// Admin Articles
Route::get('/admin/articel', [ArticleController::class, 'index'])->name('admin.articel.index');
Route::get('/admin/articel/create', [ArticleController::class, 'create'])->name('admin.articel.create');
Route::post('/admin/articel/store', [ArticleController::class, 'store'])->name('admin.articel.store');
Route::delete('/admin/articel/{id}', [ArticleController::class, 'destroy'])->name('admin.articel.destroy');
Route::get('/admin/articel/{id}/edit', [ArticleController::class, 'edit'])->name('admin.articel.edit');
Route::put('/admin/articel/{id}', [ArticleController::class, 'update'])->name('admin.articel.update');

// Laporan
Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan');
Route::get('/admin/laporan/{id}', [laporancontroller::class, 'detail'])->name('admin.laporan.detail');
Route::post('/admin/laporan/{id}/set-status', [laporancontroller::class, 'setStatus'])->name('admin.laporan.setStatus');
Route::delete('/admin/laporan/{id}', [LaporanController::class, 'destroy'])->name('admin.laporan.delete');
Route::get('/admin/laporan/{id}/download', [laporancontroller::class, 'downloadPDF'])->name('admin.laporan.download');

//Pengaturan
Route::get('/admin/pengaturan', [PengaturanController::class, 'index'])->name('admin.pengaturan');
Route::get('/admin/tambah-admin', [PengaturanController::class, 'formTambahAdmin'])->name('admin.tambahAdmin');
Route::post('/admin/tambah-admin', [PengaturanController::class, 'tambahAdmin'])->name('admin.storeAdmin');
Route::delete('/admin/pengaturan/{uid}', [PengaturanController::class, 'hapusAdmin'])->name('admin.hapusAdmin');

// Stunting
Route::get('/admin/stunting/chart', [stuntingcontroller::class, 'chart'])->name('admin.stunting.chart');
