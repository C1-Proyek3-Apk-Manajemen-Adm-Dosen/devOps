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
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Dosen\DosenController;
use App\Http\Controllers\ShareLinkController;

//Kaprodi Controller
use App\Http\Controllers\Kaprodi\DashboardController as KaprodiDashboardController;
use App\Http\Controllers\Kaprodi\NotificationController as KaprodiNotificationController;
use App\Http\Controllers\Kaprodi\DaftarDokumenController;

use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;
use App\Models\AccessControl;


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
Route::get('/dokumen/{id}/share-link', [ShareLinkController::class, 'generate']);
Route::get('/share/{hash}', [ShareLinkController::class, 'open']);



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
        Route::get('/tu/dokumen/{id}/download', [MonitoringController::class, 'download'])
            ->name('tu.dokumen.download');
            
        Route::get('/dokumen/{dokumen_id}/modal-data', [MonitoringController::class, 'getModalData'])
            ->whereNumber('dokumen_id')
            ->name('tu.dokumen.modal.data');

        Route::post('/dokumen/{id}/hak-akses', [MonitoringController::class, 'updateHakAkses'])->name('tu.update-hak-akses');
        Route::delete('/dokumen/{id}/hak-akses', [MonitoringController::class, 'removeHakAkses'])->name('tu.hak-akses.remove');
        Route::post('/dokumen/{id}/upload-versi', [MonitoringController::class, 'uploadVersi'])
            ->name('tu.upload-versi');

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

        Route::get('/dashboard', [DosenDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/dokumen', [DosenController::class, 'dokumenSaya'])
            ->name('dokumen');

        Route::post('/dokumen/{id}/hak-akses', [DosenController::class, 'updateHakAkses'])
            ->name('update-hak-akses');
        
        Route::delete('/dokumen/{id}/hak-akses', [DosenController::class, 'removeHakAkses'])
            ->name('hak-akses.remove');

        Route::get('/dokumen/{id}/detail', [DosenController::class, 'detailDokumen'])
            ->name('detail-dokumen');
        
        Route::post('/dokumen/{id}/upload-versi', [DosenController::class, 'uploadVersi'])
            ->name('upload-versi');

        Route::get('/dokumen/{id}/download', [DosenController::class, 'download'])
            ->name('dokumen.download');

        Route::get('/portofolio', fn() => view('dosen.portofolio'))
            ->name('portofolio');
        
        Route::get('/riwayat', [\App\Http\Controllers\Dosen\RiwayatUploadController::class, 'index'])
            ->name('riwayat');

        Route::get('/upload', [UploadDokumenDosenController::class, 'create'])
            ->name('upload');

        Route::post('/upload', [UploadDokumenDosenController::class, 'store'])
            ->name('dokumen.upload.store');
        
        Route::get('/notifikasi', [\App\Http\Controllers\Dosen\NotificationController::class, 'index'])
            ->name('notifikasi');

        Route::get('/riwayat/{dokumen_id}', [\App\Http\Controllers\Dosen\RiwayatUploadController::class, 'show'])
            ->whereNumber('dokumen_id')
            ->name('riwayat.show');

        Route::get('/dokumen/{dokumen_id}/modal-data', [DosenController::class, 'getModalData'])
            ->whereNumber('dokumen_id')
            ->name('dokumen.modal.data');
            
        Route::get('/portofolio', [\App\Http\Controllers\Dosen\PortofolioController::class, 'index'])
            ->name('portofolio');

        Route::get('/portofolio/search', [\App\Http\Controllers\Dosen\PortofolioController::class, 'searchForm'])
            ->name('portofolio.search');

        Route::post('/portofolio/search-pddikti', [\App\Http\Controllers\Dosen\PortofolioController::class, 'searchPddikti'])
            ->name('portofolio.search-pddikti');

        Route::post('/portofolio/import', [\App\Http\Controllers\Dosen\PortofolioController::class, 'importFromPddikti'])
            ->name('portofolio.import');

        Route::put('/portofolio/update', [\App\Http\Controllers\Dosen\PortofolioController::class, 'updateManual'])
            ->name('portofolio.update');

        Route::post('/portofolio/verify', [\App\Http\Controllers\Dosen\PortofolioController::class, 'verifyProfile'])
            ->name('portofolio.verify');

        Route::post('/portofolio/refresh', [\App\Http\Controllers\Dosen\PortofolioController::class, 'refreshFromPddikti'])
            ->name('portofolio.refresh');
    });

/*
|--------------------------------------------------------------------------
| KOORDINATOR / KAPRODI
|--------------------------------------------------------------------------
*/
Route::prefix('kaprodi')
    ->middleware(['auth', 'checkRole:koordinator'])
    ->name('kaprodi.')
    ->group(function () {

        Route::get('/dashboard', [KaprodiDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/review', fn() => view('kaprodi.review'))
            ->name('review');

        Route::get('/daftar', [DaftarDokumenController::class, 'index'])
            ->name('daftar');

        Route::get('/notifikasi', [KaprodiNotificationController::class, 'index'])
            ->name('notifikasi'); 

        Route::get('/dokumen/{dokumen_id}', [DaftarDokumenController::class, 'show'])
            ->whereNumber('dokumen_id')
            ->name('dokumen.show');

                
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
