<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\Tu\DashboardController;
use App\Http\Controllers\Tu\NotificationController;
use App\Http\Controllers\Tu\MonitoringController;
use App\Http\Controllers\Dosen\DosenController;
use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;
use App\Http\Controllers\TU\RiwayatController;

// ==================== ROOT -> LOGIN ====================
Route::get('/', fn () => redirect()->route('login'));

// ==================== AUTH ====================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== TU ====================
Route::prefix('tu')->middleware(['auth', 'checkRole:tu'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('tu.dashboard');
    Route::get('/dokumen-saya', fn() => view('tu.dokumen-saya'))->name('tu.dokumen');

    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('tu.monitoring');
    Route::get('/dokumen/{id}/detail', [MonitoringController::class, 'detailPage'])->name('tu.detail-dokumen');
    Route::get('/dokumen/{id}/download', [MonitoringController::class, 'download'])->name('tu.dokumen.download');
    Route::get('/dokumen/{id}/hak-akses', [MonitoringController::class, 'editHakAkses'])->name('tu.edit-hak-akses');
    Route::post('/dokumen/{id}/hak-akses', [MonitoringController::class, 'updateHakAkses'])->name('tu.update-hak-akses');
    Route::delete('/dokumen/{id}/hak-akses', [MonitoringController::class, 'removeHakAkses'])->name('tu.hak-akses.remove');

    // GET = tampilkan halaman upload (buka modal dsb)
    Route::get('/upload-dokumen', function () {
        $kategoris = Kategori::select('kategori_id', 'nama_kategori')->orderBy('nama_kategori')->get();
        $users = User::selectRaw('id_user as id, nama_lengkap as name, email')->orderBy('nama_lengkap')->get();
        $dokumens = Dokumen::orderByDesc('dokumen_id')->get();

        return view('tu.upload-dokumen', compact('kategoris', 'users', 'dokumens'));
    })->name('tu.upload');

    // POST = simpan file ke MinIO lewat DokumenController
    Route::post('/upload-dokumen', [DokumenController::class, 'store'])->name('tu.upload.store');

    // Riwayat TU
    Route::get('/riwayat-upload', [RiwayatController::class, 'index'])->name('tu.riwayat');
    Route::get('/dokumen/{dokumen_id}', [RiwayatController::class, 'show'])
        ->whereNumber('dokumen_id')->name('tu.dokumen.show');

    // Notifikasi TU
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('tu.notifikasi');
});

Route::prefix('dosen')
    ->middleware(['auth', 'checkRole:dosen'])
    ->name('dosen.')
    ->group(function () {

        Route::get('/dashboard', [\App\Http\Controllers\Dosen\DashboardController::class, 'index'])
            ->name('dashboard');  // → hasil: dosen.dashboard

        Route::get('/dokumen', [DosenController::class, 'dokumenSaya'])
            ->name('dokumen'); 

        Route::get('/dokumen/{id}/hak-akses', [DosenController::class, 'editHakAkses'])
            ->name('edit-hak-akses');
        
        Route::post('/dokumen/{id}/hak-akses', [DosenController::class, 'updateHakAkses'])
            ->name('update-hak-akses');
        
        Route::delete('/dokumen/{id}/hak-akses', [DosenController::class, 'removeHakAkses'])
            ->name('hak-akses.remove');

        Route::get('/dokumen/{id}/detail', [DosenController::class, 'detailDokumen'])
            ->name('detail-dokumen'); // Hasil: dosen.detail-dokumen

        Route::get('/dokumen/{id}/download', [DosenController::class, 'download'])
            ->name('dokumen.download'); // Hasil: dosen.dokumen.download

        Route::get('/upload', fn() => view('dosen.upload'))->name('upload');
        Route::get('/portofolio', fn() => view('dosen.portofolio'))->name('portofolio');

        Route::get('/riwayat-upload', [\App\Http\Controllers\Dosen\RiwayatUploadController::class, 'index'])
            ->name('riwayat-upload');

        Route::get('/notifikasi', [\App\Http\Controllers\Dosen\NotificationController::class, 'index'])
            ->name('notifikasi'); // → hasil: dosen.notifikasi
});

// ==================== KOORDINATOR ====================
Route::prefix('kaprodi')->middleware(['auth', 'checkRole:koordinator'])->group(function () {
    Route::get('/dashboard', fn() => view('kaprodi.dashboard'))->name('kaprodi.dashboard');
    Route::get('/review', fn() => view('kaprodi.review'))->name('kaprodi.review');
    Route::get('/daftar', fn() => view('kaprodi.daftar'))->name('kaprodi.daftar');
    Route::get('/notifikasi', [\App\Http\Controllers\Kaprodi\NotificationController::class, 'index'])
    ->name('kaprodi.notifikasi');
});

// ==================== DOKUMEN (WEB PAGES) ====================
Route::get('/dokumen', [DokumenController::class, 'indexPage'])->name('dokumen.page');

// CRUD dokumen (protected)
Route::middleware(['auth'])->group(function () {
    Route::post('/dokumen', [DokumenController::class, 'store'])->name('dokumen.store');
    Route::put('/dokumen/{id}', [DokumenController::class, 'update'])->name('dokumen.update');
    Route::delete('/dokumen/{id}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
});

// Open file publik (redirect ke URL MinIO)
Route::get('/dokumen/{id}/open', [DokumenController::class, 'open'])->name('dokumen.open');

// ==================== DOKUMEN (AJAX/JSON) ====================
Route::get('/dokumen-data', [DokumenController::class, 'indexJson'])->name('dokumen.data');

Route::prefix('api')->group(function () {
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');          // ?q=&status=&kategori_id=&per_page=
    Route::get('/dokumen/{id}', [DokumenController::class, 'show'])->name('dokumen.show.api');   // detail JSON
    Route::get('/dokumen/{id}/url', [DokumenController::class, 'url'])->name('dokumen.url');     // URL publik MinIO (JSON)
});

// ==================== HEALTH CHECK ====================
Route::get('/db-health', function () {
    $row = DB::selectOne("select current_database() db, current_user u, now() ts");
    return response()->json([
        'ok'   => true,
        'db'   => $row->db ?? null,
        'user' => $row->u ?? null,
        'time' => $row->ts ?? null,
    ]);
});
