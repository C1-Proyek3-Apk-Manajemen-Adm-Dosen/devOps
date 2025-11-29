<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', '!=', 'administrator')->count();
        $totalTU = User::where('role', 'tu')->count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalKoordinator = User::where('role', 'koordinator')->count();

        // Aktivitas pembuatan akun (30 hari terakhir)
        $aktivitas = DB::table('users')
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->where('role', '!=', 'administrator')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $tanggal = $aktivitas->pluck('tanggal');
        $jumlah  = $aktivitas->pluck('total');

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalTU',
            'totalDosen',
            'totalKoordinator',
            'tanggal',
            'jumlah'
        ));
    }
}