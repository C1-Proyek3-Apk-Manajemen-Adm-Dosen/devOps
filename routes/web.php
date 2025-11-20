<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;

// TU Controllers
use App\Http\Controllers\Tu\DashboardController;
use App\Http\Controllers\Tu\NotificationController;
use App\Http\Controllers\Tu\MonitoringController;
use App\Http\Controllers\TU\RiwayatController;

// Dosen Controller
use App\Http\Controllers\Dosen\UploadDokumenDosenController;

use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;


/*
|--------------------------------------------------------------------------
| ROOT → LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



/*
|--------------------------------------------------------------------------
| TATA USAHA (TU)
|--------------------------------------------------------------------------
*/
Route::prefix('tu')
    ->middleware(['auth', 'checkRole:tu'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('tu.dashboard');

        // Dokumen Saya
        Route::get('/dokumen-saya', fn() => view('tu.dokumen-saya'))->name('tu.dokumen');

        // Monitoring
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('tu.monitoring');
        Route::get('/dokumen/{id}/detail', [MonitoringController::class, 'detailPage'])->name('tu.detail-dokumen');
        Route::get('/dokumen/{id}/download', [MonitoringController::class, 'download'])->name('tu.dokumen.download');
        Route::get('/dokumen/{id}/hak-akses', [MonitoringController::class, 'editHakAkses'])->name('tu.edit-hak-akses');
        Route::post('/dokumen/{id}/hak-akses', [MonitoringController::class, 'updateHakAkses'])->name('tu.update-hak-akses');
        Route::delete('/dokumen/{id}/hak-akses', [MonitoringController::class, 'removeHakAkses'])->name('tu.hak-akses.remove');

        // Upload Dokumen (GET form)
        Route::get('/upload-dokumen', function () {
            $kategoris = Kategori::select('kategori_id', 'nama_kategori')->orderBy('nama_kategori')->get();
            $users = User::selectRaw('id_user as id, nama_lengkap as name, email')->orderBy('nama_lengkap')->get();
            $dokumens = Dokumen::orderByDesc('dokumen_id')->get();
            return view('tu.upload-dokumen', compact('kategoris', 'users', 'dokumens'));
        })->name('tu.upload');

        // Upload Dokumen (POST store)
        Route::post('/upload-dokumen', [DokumenController::class, 'store'])->name('tu.upload.store');

        // Riwayat TU
        Route::get('/riwayat-upload', [RiwayatController::class, 'index'])->name('tu.riwayat');
        Route::get('/dokumen/{dokumen_id}', [RiwayatController::class, 'show'])
            ->whereNumber('dokumen_id')->name('tu.dokumen.show');

        // Notifikasi TU
        Route::get('/notifikasi', [NotificationController::class, 'index'])->name('tu.notifikasi');
    });



/*
|--------------------------------------------------------------------------
| DOSEN
|--------------------------------------------------------------------------
*/
Route::prefix('dosen')
    ->middleware(['auth', 'checkRole:dosen'])
    ->name('dosen.')
    ->group(function () {

        // Menu statis
        Route::get('/dashboard', fn() => view('dosen.dashboard'))->name('dashboard');
        Route::get('/dokumen', fn() => view('dosen.dokumen'))->name('dokumen');
        Route::get('/portofolio', fn() => view('dosen.portofolio'))->name('portofolio');
        Route::get('/riwayat', fn() => view('dosen.riwayat'))->name('riwayat');

        // Upload - GET
        Route::get('/upload', [UploadDokumenDosenController::class, 'create'])
            ->name('upload');

        // Upload - POST
        Route::post('/upload', [UploadDokumenDosenController::class, 'store'])
            ->name('dokumen.upload.store');
});




/*
|--------------------------------------------------------------------------
| KOORDINATOR / KAPRODI
|--------------------------------------------------------------------------
*/
Route::prefix('kaprodi')
    ->middleware(['auth', 'checkRole:koordinator'])
    ->group(function () {

        Route::get('/dashboard', fn() => view('kaprodi.dashboard'))->name('kaprodi.dashboard');
        Route::get('/review', fn() => view('kaprodi.review'))->name('kaprodi.review');
        Route::get('/daftar', fn() => view('kaprodi.daftar'))->name('kaprodi.daftar');
    });



/*
|--------------------------------------------------------------------------
| DOKUMEN (WEB PAGES)
|--------------------------------------------------------------------------
*/
Route::get('/dokumen', [DokumenController::class, 'indexPage'])->name('dokumen.page');

Route::middleware(['auth'])
    ->group(function () {
        Route::post('/dokumen', [DokumenController::class, 'store'])->name('dokumen.store');
        Route::put('/dokumen/{id}', [DokumenController::class, 'update'])->name('dokumen.update');
        Route::delete('/dokumen/{id}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
    });

// Open file publik MinIO
Route::get('/dokumen/{id}/open', [DokumenController::class, 'open'])->name('dokumen.open');



/*
|--------------------------------------------------------------------------
| DOKUMEN (AJAX / JSON API)
|--------------------------------------------------------------------------
*/
Route::get('/dokumen-data', [DokumenController::class, 'indexJson'])->name('dokumen.data');

Route::prefix('api')->group(function () {
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{id}', [DokumenController::class, 'show'])->name('dokumen.show.api');
    Route::get('/dokumen/{id}/url', [DokumenController::class, 'url'])->name('dokumen.url');
});



/*
|--------------------------------------------------------------------------
| HEALTH CHECK
|--------------------------------------------------------------------------
*/
Route::get('/db-health', function () {
    $row = DB::selectOne("select current_database() db, current_user u, now() ts");
    return response()->json([
        'ok'   => true,
        'db'   => $row->db ?? null,
        'user' => $row->u ?? null,
        'time' => $row->ts ?? null,
    ]);
});
