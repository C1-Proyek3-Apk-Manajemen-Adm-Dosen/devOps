<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\TU\MonitoringController;
use App\Models\Dokumen;
use App\Models\Kategori;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('tu')->middleware(['auth', 'checkRole:tu'])->group(function () {

    Route::get('/dashboard', fn() => view('tu.dashboard'))->name('tu.dashboard');

    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('tu.monitoring');

    Route::get('/dokumen/{id}/detail', [MonitoringController::class, 'detailPage'])->name('tu.detail-dokumen');

    Route::get('/dokumen/{id}/hak-akses', [MonitoringController::class, 'editHakAkses'])->name('tu.edit-hak-akses');

    Route::post('/dokumen/{id}/hak-akses', [MonitoringController::class, 'updateHakAkses'])->name('tu.update-hak-akses');

    Route::delete('/dokumen/{id}/hak-akses', [MonitoringController::class, 'removeHakAkses'])->name('tu.hak-akses.remove');

    Route::get('/upload-dokumen', function () {
        $kategoris = Kategori::select('kategori_id', 'nama_kategori')->orderBy('nama_kategori')->get();
        $dokumens = Dokumen::orderByDesc('dokumen_id')->get();
        return view('tu.upload-dokumen', compact('kategoris', 'dokumens'));
    })->name('tu.upload');

    Route::post('/upload-dokumen', [DokumenController::class, 'store'])->name('tu.upload.store');

    Route::get('/riwayat-upload', fn() => view('tu.riwayat-upload'))->name('tu.riwayat');
});

Route::prefix('dosen')->middleware(['auth', 'checkRole:dosen'])->group(function () {
    Route::get('/dashboard', fn() => view('dosen.dashboard'))->name('dosen.dashboard');
    Route::get('/dokumen', fn() => view('dosen.dokumen'))->name('dosen.dokumen');
    Route::get('/upload', fn() => view('dosen.upload'))->name('dosen.upload');
    Route::get('/portofolio', fn() => view('dosen.portofolio'))->name('dosen.portofolio');
    Route::get('/riwayat', fn() => view('dosen.riwayat'))->name('dosen.riwayat');
});

Route::prefix('kaprodi')->middleware(['auth', 'checkRole:koordinator'])->group(function () {
    Route::get('/dashboard', fn() => view('kaprodi.dashboard'))->name('kaprodi.dashboard');
    Route::get('/review', fn() => view('kaprodi.review'))->name('kaprodi.review');
    Route::get('/daftar', fn() => view('kaprodi.daftar'))->name('kaprodi.daftar');
});

Route::get('/dokumen', [DokumenController::class, 'indexPage'])->name('dokumen.page');

Route::middleware(['auth'])->group(function () {
    Route::post('/dokumen', [DokumenController::class, 'store'])->name('dokumen.store');
    Route::put('/dokumen/{id}', [DokumenController::class, 'update'])->name('dokumen.update');
    Route::delete('/dokumen/{id}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
});

Route::get('/dokumen/{id}/open', [DokumenController::class, 'open'])->name('dokumen.open');

Route::get('/dokumen-data', [DokumenController::class, 'indexJson'])->name('dokumen.data');

Route::prefix('api')->group(function () {
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{id}', [DokumenController::class, 'show'])->name('dokumen.show.api');
    Route::get('/dokumen/{id}/url', [DokumenController::class, 'url'])->name('dokumen.url');
});

Route::get('/db-health', function () {
    $row = DB::selectOne("select current_database() db, current_user u, now() ts");

    return response()->json([
        'ok'   => true,
        'db'   => $row->db ?? null,
        'user' => $row->u ?? null,
        'time' => $row->ts ?? null,
    ]);
});
