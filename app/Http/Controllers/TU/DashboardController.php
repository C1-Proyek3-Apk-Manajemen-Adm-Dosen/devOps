<?php

namespace App\Http\Controllers\Tu;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // === Statistik Dokumen Berdasarkan Kategori ===
        $totalKeputusan = DB::table('dokumen')
            ->join('kategori', 'dokumen.kategori_id', '=', 'kategori.kategori_id')
            ->where('kategori.nama_kategori', 'ilike', '%Keputusan%')
            ->count();

        $totalTugas = DB::table('dokumen')
            ->join('kategori', 'dokumen.kategori_id', '=', 'kategori.kategori_id')
            ->where('kategori.nama_kategori', 'ilike', '%Tugas%')
            ->count();

        $totalPengajaran = DB::table('dokumen')
            ->join('kategori', 'dokumen.kategori_id', '=', 'kategori.kategori_id')
            ->where('kategori.nama_kategori', 'ilike', '%Rencana Pembelajaran%')
            ->count();

        // === Aktivitas Upload Dokumen (30 hari terakhir) ===
        $aktivitas = DB::table('dokumen')
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $tanggal = $aktivitas->pluck('tanggal');
        $jumlah = $aktivitas->pluck('total');

        // === Notifikasi terbaru (akses dokumen ke TU) ===
        $userId = Auth::id();
        $notifikasi = DB::table('access_control')
            ->join('dokumen', 'access_control.document_id', '=', 'dokumen.dokumen_id')
            ->join('users', 'access_control.created_by', '=', 'users.id_user')
            ->where('access_control.grantee_user_id', $userId)
            ->select('users.nama_lengkap', 'dokumen.judul', 'access_control.created_at')
            ->orderBy('access_control.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('tu.dashboard', compact(
            'totalKeputusan', 'totalTugas', 'totalPengajaran',
            'tanggal', 'jumlah', 'notifikasi'
        ));
    }
}
