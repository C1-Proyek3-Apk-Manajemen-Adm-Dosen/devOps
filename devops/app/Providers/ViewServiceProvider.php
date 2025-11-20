<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\AccessControl;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // === Kirim notifikasi otomatis ke dropdown ===
        View::composer('components.notification-dropdown', function ($view) {
            $userId = Auth::id();
            $recentNotifikasi = collect();

            if ($userId) {
                $recentNotifikasi = AccessControl::with([
                        'pemberiAkses:id_user,nama_lengkap',
                        'dokumen:dokumen_id,judul'
                    ])
                    ->where('grantee_user_id', $userId) // penerima hak akses
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get();
            }

            $view->with('recentNotifikasi', $recentNotifikasi);
        });
    }
}
