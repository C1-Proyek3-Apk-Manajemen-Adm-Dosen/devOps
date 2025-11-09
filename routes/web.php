<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Storage;

// ==================== REDIRECT KE LOGIN ====================
Route::get('/', function () {
    return redirect()->route('login'); // kalau buka root, langsung ke login
});

// ==================== LOGIN & LOGOUT ====================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== TU ====================
Route::prefix('tu')->middleware(['auth', 'checkRole:tu'])->group(function () {
    Route::get('/dashboard', fn() => view('tu.dashboard'))->name('tu.dashboard');
    Route::get('/dokumen-saya', fn() => view('tu.dokumen-saya'))->name('tu.dokumen');
    Route::get('/upload-dokumen', fn() => view('tu.upload-dokumen'))->name('tu.upload');
    Route::get('/riwayat-upload', fn() => view('tu.riwayat-upload'))->name('tu.riwayat');
    
    // RUTE BARU UNTUK PROSES UPLOAD (TAMBAHAN DI SINI)
    Route::post('/upload-dokumen', [DokumenController::class, 'store'])->name('tu.upload.post');
});

// ==================== DOSEN ====================
Route::prefix('dosen')->middleware(['auth', 'checkRole:dosen'])->group(function () {
    Route::get('/dashboard', fn() => view('dosen.dashboard'))->name('dosen.dashboard');
    Route::get('/dokumen', fn() => view('dosen.dokumen'))->name('dosen.dokumen');
    Route::get('/upload', fn() => view('dosen.upload'))->name('dosen.upload');
    Route::get('/portofolio', fn() => view('dosen.portofolio'))->name('dosen.portofolio');
    Route::get('/riwayat', fn() => view('dosen.riwayat'))->name('dosen.riwayat');

    // RUTE BARU UNTUK PROSES UPLOAD (TAMBAHAN DI SINI)
    Route::post('/upload', [DokumenController::class, 'store'])->name('dosen.upload.post');
});

// ==================== KOORDINATOR ====================
Route::prefix('kaprodi')->middleware(['auth', 'checkRole:koordinator'])->group(function () {
    Route::get('/dashboard', fn() => view('kaprodi.dashboard'))->name('kaprodi.dashboard');
    Route::get('/review', fn() => view('kaprodi.review'))->name('kaprodi.review');
    Route::get('/daftar', fn() => view('kaprodi.daftar'))->name('kaprodi.daftar');
});

// ==================== DOKUMEN (AJAX/JSON) ====================
Route::get('/dokumen', [DokumenController::class, 'indexPage']);
Route::get('/dokumen-data', [DokumenController::class, 'indexJson'])->name('dokumen.data');
Route::get('/dokumen/{id}', [DokumenController::class, 'show']);

// ==================== HEALTH CHECK ====================
Route::get('/db-health', function () {
    $row = DB::selectOne("select current_database() db, current_user u, now() ts");
    return response()->json(['ok'=>true,'db'=>$row->db??null,'user'=>$row->u??null,'time'=>$row->ts??null]);
});

//

Route::get('/minio-test', function () {
    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
    $disk = Storage::disk('minio');

    $disk->put('health/hello.txt', 'Hello from Laravel!', [
        'visibility' => 'public',
    ]);

    $publicUrl = $disk->url('health/hello.txt'); // butuh bucket public (sudah 'download')
    $signedUrl = $disk->temporaryUrl('health/hello.txt', now()->addMinutes(10)); // selalu works

    return compact('publicUrl', 'signedUrl');
});