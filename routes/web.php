<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\dashboardcontroller;
use App\Http\Controllers\stuntingcontroller;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin Auth
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Forgot Password
Route::get('/admin/forgot-password', [AuthController::class, 'showForgotPassword'])->name('admin.forgot-password');
Route::post('/admin/forgot-password/send', [AuthController::class, 'sendPasswordResetEmail'])->name('admin.forgot-password.send');
Route::get('/admin/reset-password', [AuthController::class, 'showResetPassword'])->name('admin.reset-password');
Route::post('/admin/reset-password', [AuthController::class, 'resetPassword'])->name('admin.reset-password');

// Admin dashboard
Route::get('/admin/dashboard', [dashboardcontroller::class, 'index'])->name('admin.dashboard');

// Admin Articles
Route::get('/admin/articel', [ArticleController::class, 'index'])->name('admin.articel.index');
Route::get('/admin/articel/export/csv', [ArticleController::class, 'downloadList'])->name('admin.articel.downloadList');
Route::get('/admin/articel/create', [ArticleController::class, 'create'])->name('admin.articel.create');
Route::post('/admin/articel/store', [ArticleController::class, 'store'])->name('admin.articel.store');
Route::delete('/admin/articel/{id}', [ArticleController::class, 'destroy'])->name('admin.articel.destroy');
Route::get('/admin/articel/{id}/edit', [ArticleController::class, 'edit'])->name('admin.articel.edit');
Route::put('/admin/articel/{id}', [ArticleController::class, 'update'])->name('admin.articel.update');

// Laporan
Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan');
Route::get('/admin/laporan/export/csv', [laporancontroller::class, 'downloadList'])->name('admin.laporan.downloadList');
Route::get('/admin/laporan/{id}', [laporancontroller::class, 'detail'])->name('admin.laporan.detail');
Route::post('/admin/laporan/{id}/set-status', [laporancontroller::class, 'setStatus'])->name('admin.laporan.setStatus');
Route::delete('/admin/laporan/{id}', [LaporanController::class, 'destroy'])->name('admin.laporan.delete');
Route::get('/admin/laporan/{id}/download', [laporancontroller::class, 'downloadPDF'])->name('admin.laporan.download');

// Laporan Chat
Route::get('/admin/laporan/{id}/chat', [laporancontroller::class, 'chat'])->name('admin.laporan.chat');
Route::get('/admin/laporan/{id}/chat/messages', [laporancontroller::class, 'chatMessages'])->name('admin.laporan.chat.messages');
Route::post('/admin/laporan/{id}/chat/send', [laporancontroller::class, 'sendChat'])->name('admin.laporan.chat.send');
Route::delete('/admin/laporan/{id}/chat/{messageId}', [laporancontroller::class, 'deleteChat'])->name('admin.laporan.chat.delete');
Route::put('/admin/laporan/{id}/chat/{messageId}', [laporancontroller::class, 'updateChat'])->name('admin.laporan.chat.update');

//Pengaturan
Route::get('/admin/pengaturan', [PengaturanController::class, 'index'])->name('admin.pengaturan');
Route::post('/admin/tambah-admin', [PengaturanController::class, 'tambahAdmin'])->name('admin.storeAdmin');
Route::delete('/admin/pengaturan/{uid}', [PengaturanController::class, 'hapusAdmin'])->name('admin.hapusAdmin');

// Stunting
Route::get('/admin/stunting/chart', [stuntingcontroller::class, 'chart'])->name('admin.stunting.chart');

// Chart routes removed

// Profile
Route::get('/admin/profile', [ProfileController::class, 'index'])->name('admin.profile');
Route::post('/admin/profile/update-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update-password');
