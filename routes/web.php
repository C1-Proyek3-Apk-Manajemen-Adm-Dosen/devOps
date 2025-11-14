<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;

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
    Route::get('/dashboard', fn() => view('tu.dashboard'))->name('tu.dashboard');
    Route::get('/dokumen-saya', fn() => view('tu.dokumen-saya'))->name('tu.dokumen');

    // ✅ GET = tampilkan halaman upload (buka modal dsb)
    Route::get('/upload-dokumen', function () {
        // Ambil kategori untuk dropdown
        $kategoris = \App\Models\Kategori::select('kategori_id', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();

        // Ambil user (pakai id_user & nama_lengkap)
        $users = \App\Models\User::selectRaw('id_user as id, nama_lengkap as name, email')
            ->orderBy('nama_lengkap')
            ->get();

        // Ambil daftar dokumen (kalau diperlukan)
        $dokumens = \App\Models\Dokumen::orderByDesc('dokumen_id')->get();

        return view('tu.upload-dokumen', compact('kategoris', 'users', 'dokumens'));
    })->name('tu.upload');

    // ✅ POST = simpan file ke MinIO lewat DokumenController
    Route::post('/upload-dokumen', [DokumenController::class, 'store'])->name('tu.upload.store');
    Route::post('/store', [DokumenController::class, 'store'])->name('tu.store');

    Route::get('/riwayat-upload', fn() => view('tu.riwayat-upload'))->name('tu.riwayat');
});

// ==================== DOSEN ====================
Route::prefix('dosen')->middleware(['auth', 'checkRole:dosen'])->group(function () {
    Route::get('/dashboard', fn() => view('dosen.dashboard'))->name('dosen.dashboard');
    Route::get('/dokumen', fn() => view('dosen.dokumen'))->name('dosen.dokumen');
    Route::get('/upload', fn() => view('dosen.upload'))->name('dosen.upload');
    Route::get('/portofolio', fn() => view('dosen.portofolio'))->name('dosen.portofolio');
    Route::get('/riwayat', fn() => view('dosen.riwayat'))->name('dosen.riwayat');
});

// ==================== KOORDINATOR ====================
Route::prefix('kaprodi')->middleware(['auth', 'checkRole:koordinator'])->group(function () {
    Route::get('/dashboard', fn() => view('kaprodi.dashboard'))->name('kaprodi.dashboard');
    Route::get('/review', fn() => view('kaprodi.review'))->name('kaprodi.review');
    Route::get('/daftar', fn() => view('kaprodi.daftar'))->name('kaprodi.daftar');
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
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{id}', [DokumenController::class, 'show'])->name('dokumen.show.api');
    Route::get('/dokumen/{id}/url', [DokumenController::class, 'url'])->name('dokumen.url');
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
