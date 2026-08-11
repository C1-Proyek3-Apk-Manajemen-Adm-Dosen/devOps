<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;
use App\Models\VersiDokumen;

class DaftarDokumenController extends Controller
{
    /**
     * Halaman daftar dokumen Kaprodi
     */
    public function index(Request $request)
    {
        // =========================
        // 1. Query dasar dokumen
        // =========================
        $query = Dokumen::query()
            ->leftJoin('kategori', 'kategori.kategori_id', '=', 'dokumen.kategori_id')
            ->leftJoin('users', 'users.id_user', '=', 'dokumen.created_by');

        // =========================
        // 2. Search (case-insensitive)
        // =========================
        if ($request->filled('q')) {
            $term = strtolower(trim($request->q));

            $query->where(function ($q) use ($term) {
                $q->whereRaw("LOWER(COALESCE(dokumen.judul, '')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("LOWER(COALESCE(dokumen.nomor_dokumen, '')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("LOWER(COALESCE(kategori.nama_kategori, '')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("LOWER(COALESCE(users.nama_lengkap, '')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("LOWER(to_char(dokumen.created_at, 'DD FMMonth YYYY')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("to_char(dokumen.created_at, 'YYYY') LIKE ?", ["%{$term}%"]);
            });
        }

        // =========================
        // 3. Filter kategori
        // =========================
        if ($request->filled('kategori_id') && $request->kategori_id !== 'all') {
            $query->where('dokumen.kategori_id', $request->kategori_id);
        }

        // =========================
        // 4. Filter status (PENDING / REVISI / ACC / TOLAK)
        // =========================
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('dokumen.status', strtoupper($request->status));
        }

        // =========================
        // 5. Filter periode (hari)
        // =========================
        if ($request->filled('period') && $request->period !== 'all') {
            $days = (int) $request->period;
            if ($days > 0) {
                $query->where('dokumen.created_at', '>=', now()->subDays($days));
            }
        }

        // =========================
        // 6. Filter dosen
        // =========================
        if ($request->filled('dosen_id') && $request->dosen_id !== 'all') {
            $query->where('dokumen.created_by', $request->dosen_id);
        }

        // =========================
        // 7. Ambil data + paginasi (5 per halaman)
        // =========================
        $docs = $query
            ->select([
                'dokumen.dokumen_id',
                'dokumen.judul',
                'dokumen.nomor_dokumen',
                'dokumen.created_at',
                'dokumen.status',
                'kategori.nama_kategori',
                'users.nama_lengkap as dosen_nama',
            ])
            ->orderByDesc('dokumen.created_at')
            ->paginate(5)
            ->withQueryString();

        // =========================
        // 8. Options dropdown filter
        // =========================

        // Kategori (Semua jenis dokumen)
        $kategoriOptions = Kategori::orderBy('nama_kategori')
            ->get(['kategori_id', 'nama_kategori']);

        // Dosen
        $dosenOptions = User::where('role', 'dosen')
            ->orderBy('nama_lengkap')
            ->get(['id_user', 'nama_lengkap']);

        // Status
        $statusOptions = [
            'PENDING' => 'Pending',
            'REVISI'  => 'Revisi',
            'ACC'     => 'ACC',
            'TOLAK'   => 'Tolak',
        ];

        // nilai filter yang sedang dipakai (biar select-nya ke-set)
        $selectedKategori = $request->get('kategori_id', 'all');
        $selectedStatus   = $request->get('status', 'all');
        $selectedPeriod   = $request->get('period', 'all');
        $selectedDosen    = $request->get('dosen_id', 'all');
        $searchTerm       = $request->get('q', '');

        return view('kaprodi.daftar', compact(
            'docs',
            'kategoriOptions',
            'dosenOptions',
            'statusOptions',
            'selectedKategori',
            'selectedStatus',
            'selectedPeriod',
            'selectedDosen',
            'searchTerm'
        ));
    }

    /**
     * Detail dokumen Kaprodi
     */
    public function show($dokumen_id)
    {
        $dokumen = Dokumen::query()
            ->leftJoin('kategori', 'kategori.kategori_id', '=', 'dokumen.kategori_id')
            ->leftJoin('users', 'users.id_user', '=', 'dokumen.created_by')
            ->where('dokumen.dokumen_id', $dokumen_id)
            ->select('dokumen.*', 'kategori.nama_kategori', 'users.nama_lengkap')
            ->firstOrFail();

        $versi = VersiDokumen::where('dokumen_id', $dokumen_id)
            ->orderByDesc('nomor_versi')
            ->get();

        $latest = $versi->first();

        return view('kaprodi.dokumen.show', compact('dokumen', 'versi', 'latest'));
    }
}
