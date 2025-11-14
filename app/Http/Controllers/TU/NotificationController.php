<?php

namespace App\Http\Controllers\Tu;

use App\Http\Controllers\Controller;
use App\Models\AccessControl;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Halaman "Lihat semua"
    public function index()
    {
        $userId = Auth::id();

        $notifikasi = AccessControl::with(['pemberiAkses:id_user,nama_lengkap', 'dokumen:dokumen_id,judul'])
            ->where('grantee_user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('tu.notifikasi', compact('notifikasi'));
    }
}
