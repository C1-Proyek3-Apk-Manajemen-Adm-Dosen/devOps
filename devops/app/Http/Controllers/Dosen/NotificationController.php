<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AccessControl;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id_user;

        // Ambil semua notifikasi untuk DOSEN ini
        $notifikasi = AccessControl::with(['pemberiAkses', 'dokumen'])
            ->where('grantee_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('dosen.notifikasi', compact('notifikasi'));
    }
}
