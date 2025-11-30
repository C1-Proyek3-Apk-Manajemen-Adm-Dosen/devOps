<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total users berdasarkan role
        $totalUsers = User::where('role', '!=', 'administrator')->count();
        $totalTU = User::where('role', 'tu')->count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalKoordinator = User::where('role', 'koordinator')->count();

        // Aktivitas pembuatan akun (30 hari terakhir)
        $aktivitas = User::select(
            DB::raw("TO_CHAR(created_at, 'YYYY-MM-DD') as tanggal"),
            DB::raw('COUNT(*) as jumlah')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->where('role', '!=', 'administrator')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Format data untuk chart
        $chartLabels = $aktivitas->pluck('tanggal')->map(function ($date) {
            // Format tanggal ke dd/mm
            return \Carbon\Carbon::parse($date)->format('d/m');
        });

        $chartData = $aktivitas->pluck('jumlah');

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalTU' => $totalTU,
            'totalDosen' => $totalDosen,
            'totalKoordinator' => $totalKoordinator,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ]);
    }
}
