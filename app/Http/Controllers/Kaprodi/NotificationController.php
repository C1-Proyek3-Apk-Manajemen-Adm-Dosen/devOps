<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\AccessControl;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua notifikasi untuk kaprodi
        $notifikasi = AccessControl::with(['pemberiAkses', 'dokumen'])
            ->where('grantee_user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('kaprodi.notifikasi', compact('notifikasi'));
    }
}
