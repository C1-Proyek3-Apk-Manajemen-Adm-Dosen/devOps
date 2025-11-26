<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id_user;

        // Statistik dokumen (pending / acc / revisi / tolak)
        $pending = DB::table('dokumen')->where('status', 'PENDING')->count();
        $acc     = DB::table('dokumen')->where('status', 'ACC')->count();
        $revisi  = DB::table('dokumen')->where('status', 'REVISI')->count();
        $tolak   = DB::table('dokumen')->where('status', 'TOLAK')->count();

        // === Notifikasi terbaru kaprodi ===
        $notifikasi = DB::table('access_control')
            ->join('dokumen', 'access_control.document_id', '=', 'dokumen.dokumen_id')
            ->join('users', 'access_control.created_by', '=', 'users.id_user')
            ->where('access_control.grantee_user_id', $userId)
            ->select(
                'users.nama_lengkap',
                'dokumen.judul',
                'access_control.created_at'
            )
            ->orderBy('access_control.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('kaprodi.dashboard', compact(
            'pending',
            'acc',
            'revisi',
            'tolak',
            'notifikasi'
        ));
    }
}

