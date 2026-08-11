<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id_user;  // id_user dari tabel users

        // === Statistik Dokumen Dosen (pakai owner_user_id) ===
        $totalRPS = DB::table('dokumen')
            ->join('kategori', 'dokumen.kategori_id', '=', 'kategori.kategori_id')
            ->where('owner_user_id', $userId)
            ->where('kategori.nama_kategori', 'ilike', '%RPS%')
            ->count();

        $totalSKP = DB::table('dokumen')
            ->join('kategori', 'dokumen.kategori_id', '=', 'kategori.kategori_id')
            ->where('owner_user_id', $userId)
            ->where('kategori.nama_kategori', 'ilike', '%SKP%')
            ->count();

        $totalBKD = DB::table('dokumen')
            ->join('kategori', 'dokumen.kategori_id', '=', 'kategori.kategori_id')
            ->where('owner_user_id', $userId)
            ->where('kategori.nama_kategori', 'ilike', '%BKD%')
            ->count();

        // === Aktivitas Upload Dosen ===
        $aktivitas = DB::table('dokumen')
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as total'))
            ->where('owner_user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $tanggal = $aktivitas->pluck('tanggal');
        $jumlah  = $aktivitas->pluck('total');

        // === Notifikasi terbaru ===
        $notifikasi = DB::table('access_control')
        ->join('dokumen', 'access_control.document_id', '=', 'dokumen.dokumen_id')
        ->join('users', 'access_control.created_by', '=', 'users.id_user')
        ->where('access_control.grantee_user_id', Auth::user()->id_user)
        ->select(
            'users.nama_lengkap',
            'dokumen.judul',
            'access_control.created_at'
        )
        ->orderBy('access_control.created_at', 'desc')
        ->limit(5)
        ->get();



        return view('dosen.dashboard', compact(
            'totalRPS', 'totalSKP', 'totalBKD',
            'tanggal', 'jumlah', 'notifikasi'
        ));
    }
}
