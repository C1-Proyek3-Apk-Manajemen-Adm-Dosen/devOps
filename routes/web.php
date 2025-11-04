<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokumenController;


Route::get('/dokumen', [DokumenController::class, 'indexPage']);      // ⬅️ halaman tabel
Route::get('/dokumen-data', [DokumenController::class, 'indexJson'])  // ⬅️ data JSON
     ->name('dokumen.data');

Route::get('/dokumen/{id}', [DokumenController::class, 'show']);      // detail JSON (opsional)

Route::view('/login', 'auth.login');

// ==================== TU ====================
Route::prefix('tu')->group(function () {
    Route::get('/dashboard', fn() => view('tu.dashboard'))->name('tu.dashboard');
    Route::get('/dokumen-saya', fn() => view('tu.dokumen-saya'))->name('tu.dokumen');
    Route::get('/upload-dokumen', fn() => view('tu.upload-dokumen'))->name('tu.upload');
    Route::get('/riwayat-upload', fn() => view('tu.riwayat-upload'))->name('tu.riwayat');
});

// ==================== DOSEN ====================
Route::prefix('dosen')->group(function () {
    Route::get('/dashboard', fn() => view('dosen.dashboard'))->name('dosen.dashboard');
    Route::get('/dokumen', fn() => view('dosen.dokumen'))->name('dosen.dokumen');
    Route::get('/upload', fn() => view('dosen.upload'))->name('dosen.upload');
    Route::get('/portofolio', fn() => view('dosen.portofolio'))->name('dosen.portofolio');
    Route::get('/riwayat', fn() => view('dosen.riwayat'))->name('dosen.riwayat');
});

// ==================== KAPRODI ====================
Route::prefix('kaprodi')->group(function () {
    Route::get('/dashboard', fn() => view('kaprodi.dashboard'))->name('kaprodi.dashboard');
    Route::get('/review', fn() => view('kaprodi.review'))->name('kaprodi.review');
    Route::get('/daftar', fn() => view('kaprodi.daftar'))->name('kaprodi.daftar');
});



// Health check tetap ok
Route::get('/db-health', function () {
    $row = DB::selectOne("select current_database() db, current_user u, now() ts");
    return response()->json(['ok'=>true,'db'=>$row->db??null,'user'=>$row->u??null,'time'=>$row->ts??null]);
});
