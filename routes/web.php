<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers Dashboard
use App\Http\Controllers\Dashboard\DashboardController; // Umum
use App\Http\Controllers\Admin\AdminDashboardController; // Kita pakai ini sebagai Main Dashboard Controller

// Controllers Nasabah
use App\Http\Controllers\Nasabah\NasabahProfileController;
use App\Http\Controllers\Nasabah\PengajuanKreditController;
use App\Http\Controllers\Nasabah\RiwayatKreditController;
use App\Http\Controllers\Nasabah\SimulasiKreditController;

// Controllers Internal (Dipakai Bersama)
use App\Http\Controllers\Admin\AdminPengajuanController;
use App\Http\Controllers\Admin\AdminSlikController;
use App\Http\Controllers\Admin\AdminAngsuranController;
use App\Http\Controllers\Admin\AdminLaporanController;

// Controllers Spesifik Logic
use App\Http\Controllers\Manager\ManagerRekomendasiController;
use App\Http\Controllers\Manager\ManagerAngsuranController;
use App\Http\Controllers\Direktur\DirekturPersetujuanController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\MasterProductController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\SuperAdmin\NasabahController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. ROOT REDIRECT
Route::get('/', function () {
    if (Auth::check()) {
        // Jika Nasabah ke dashboard nasabah
        if (Auth::user()->hasRole('Nasabah')) {
            return redirect()->route('nasabah.dashboard');
        }
        // Selain itu (Admin/Manager/Direktur/Superadmin) masuk ke App Dashboard
        return redirect()->route('app.dashboard');
    }
    return view('welcome');
});


// ==============================================================================
// 2. ZONA NASABAH (Tetap Terpisah karena Layout & Logic Beda Jauh)
// ==============================================================================
Route::middleware(['auth', 'role:Nasabah'])
    ->prefix('nasabah')
    ->name('nasabah.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::controller(NasabahProfileController::class)->group(function () {
            Route::get('/profile', 'edit')->name('profile-edit');
            Route::patch('/profile', 'update')->name('profile-update');
            Route::put('/password', 'updatePassword')->name('password-update');
        });

        // Pengajuan Kredit
        Route::controller(PengajuanKreditController::class)
            ->prefix('pengajuan')->name('pengajuan.')
            ->group(function () {
                Route::get('/step-1', 'createStep1')->name('step1');
                Route::post('/step-1', 'postStep1')->name('step1.post');
                Route::get('/step-2', 'createStep2')->name('step2');
                Route::post('/step-2', 'postStep2')->name('step2.post');
                Route::get('/back-step-1', 'backToStep1')->name('back.step1');
                Route::get('/step-3', 'createStep3')->name('step3');
                Route::post('/step-3', 'postStep3')->name('step3.post');
                Route::post('/upload-temp', 'uploadTemp')->name('upload.temp');
                Route::get('/back-step-2', 'backToStep2')->name('back.step2');
                Route::get('/review', 'createReview')->name('review');
                Route::post('/review', 'postReview')->name('review.post');
                Route::get('/back-step-3', 'backToStep3')->name('back.step3');
            });

        // Riwayat & Simulasi
        Route::controller(RiwayatKreditController::class)->prefix('riwayat')->name('riwayat.')->group(function () {
            Route::get('/kredit', 'index')->name('index');
            Route::get('/kredit/aktif', 'aktif')->name('aktif');
            Route::get('/kredit/{id}', 'show')->name('show');
        });
        Route::controller(SimulasiKreditController::class)->prefix('simulasi')->name('simulasi.')->group(function () {
            Route::get('/kredit', 'index')->name('index');
            Route::post('/kredit/hitung', 'calculate')->name('calculate');
        });
    });


// ==============================================================================
// 3. ZONA INTERNAL APLIKASI (Unified Route)
// ==============================================================================
// Menggabungkan Admin, Manager, Direktur, Superadmin
Route::middleware(['auth', 'role:Admin|Manager|Direktur|Superadmin'])
    ->prefix('app')     // URL jadi: domain.com/app/...
    ->name('app.')      // Route Name jadi: app.dashboard, app.laporan...
    ->group(function () {

        // A. DASHBOARD (Satu Pintu)
        // Pastikan AdminDashboardController::index punya logic redirect/view sesuai role
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');


        // B. MODUL PENGAJUAN (Admin)
        Route::controller(AdminPengajuanController::class)
            ->prefix('pengajuan')
            ->name('pengajuan.')
            ->middleware('can:view_pengajuan') // Hanya yg punya izin view_pengajuan
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')
                    ->name('show')
                    ->middleware('can:detail_pengajuan'); // Hanya yg punya izin detail_pengajuan
            });


        // C. MODUL REKOMENDASI (Manager)
        Route::controller(ManagerRekomendasiController::class)
            ->prefix('rekomendasi')
            ->name('rekomendasi.')
            ->middleware('can:view_rekomendasi') // Hanya Manager/yg punya izin
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/riwayat', 'riwayat')->name('riwayat')->middleware('can:history_rekomendasi');
                Route::get('/{id}', 'show')->name('show')->middleware('can:history_rekomendasi');
                Route::put('/{id}', 'update')->name('update')->middleware('can:update_rekomendasi');
                Route::get('/{id}/detail', 'detail')->name('detail')->middleware('can:detail_rekomendasi');
            });


        // D. MODUL PERSETUJUAN (Direktur)
        Route::controller(DirekturPersetujuanController::class)
            ->prefix('persetujuan')
            ->name('persetujuan.')
            ->middleware('can:view_persetujuan')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show')->middleware('can:detail_persetujuan');
                Route::put('/{id}', 'update')->name('update')->middleware('can:update_persetujuan'); // Approve/Reject
            });


        // E. MODUL SLIK (Admin Only)
        Route::controller(AdminSlikController::class)
            ->prefix('slik')
            ->name('slik.')
            ->middleware('can:view_slik')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}/upload', 'edit')->name('edit')->middleware('can:upload_slik');
                Route::put('/{id}', 'update')->name('update')->middleware('can:upload_slik');
            });


        // F. MODUL ANGSURAN (Shared: Admin, Manager, Direktur)
        Route::prefix('angsuran')->name('angsuran.')->group(function () {
            // Controller Baca Data & Input Bayar (Admin)
            Route::controller(AdminAngsuranController::class)
                ->middleware('can:view_angsuran')
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/{id}', 'show')->name('show');
                    Route::put('/bayar/{paymentId}', 'update')->name('update')->middleware('can:update_angsuran');
                });

            // Controller Reversal (Direktur/Manager)
            Route::controller(ManagerAngsuranController::class)
                ->middleware('can:reverse_angsuran')
                ->group(function () {
                    Route::put('/reverse/{paymentId}', 'reverse')->name('reverse')->middleware('can:reverse_angsuran');
                });
        });


        // G. MODUL LAPORAN (Shared Total)
        // Tidak perlu duplicate code untuk Manager/Direktur, semua lewat sini
        Route::controller(AdminLaporanController::class)
            ->prefix('laporan')
            ->name('laporan.')
            ->group(function () {
                Route::get('/', 'index')->name('index'); // Index laporan (opsional)

                Route::get('/pengajuan', 'pengajuan')
                    ->name('pengajuan')
                    ->middleware('can:view_laporan_persetujuan_kredit');

                Route::get('/analisis', 'analisis')
                    ->name('analisis')
                    ->middleware('can:view_laporan_analisa_kredit');

                Route::get('/monitoring', 'monitoring')
                    ->name('monitoring')
                    ->middleware('can:view_laporan_monitoring_angsuran');

                Route::get('/realisasi', 'realisasi')
                    ->name('realisasi')
                    ->middleware('can:view_laporan_realisasi_pinjaman');

                Route::get('/rekapitulasi', 'rekapitulasi')
                    ->name('rekapitulasi')
                    ->middleware('can:view_laporan_rekapitulasi');
            });

        // H. MASTER PRODUCT
        Route::resource('products', MasterProductController::class)
            ->middleware('can:view_master_produk');
        
        // I. DATA NASABAH
        Route::resource('nasabah', NasabahController::class)
            ->middleware('can:view_nasabah');

        // J. MANAJEMEN USER
        Route::resource('users', UserManagementController::class)
            ->middleware('can:view_users');

        // K. SYSTEM MANAGEMENT (Superadmin)
        Route::resource('roles', RoleController::class)
            ->middleware('can:manage_roles');
    });


// ==============================================================================
// 4. SHARED AUTH ROUTES
// ==============================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

require __DIR__ . '/auth.php';
