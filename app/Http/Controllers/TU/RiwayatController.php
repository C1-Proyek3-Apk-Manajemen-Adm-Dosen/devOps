<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dokumen;
use App\Models\VersiDokumen;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RiwayatController extends Controller
{
    // Halaman Riwayat Upload TU
    public function index(Request $r)
    {
        $uid = \Illuminate\Support\Facades\Auth::id();

        // Grup alias kategori yang dikelola TU
        $groups = [
            'st' => ['st', 'surat tugas'],
            'sk' => ['sk', 'surat keputusan'],
            'rp' => ['riwayat pengajaran'],
        ];

        // Build query dasar: join kategori + batasi hanya 3 grup kategori TU
        $query = Dokumen::query()
            ->leftJoin('kategori', 'kategori.kategori_id', '=', 'dokumen.kategori_id')
            ->where(function ($q) use ($uid) {
                $q->where('dokumen.created_by', $uid)
                  ->orWhere('dokumen.owner_user_id', $uid)
                  ->orWhereNull('dokumen.created_by');
            })
            ->whereIn(
                DB::raw('LOWER(TRIM(kategori.nama_kategori))'),
                array_merge($groups['st'], $groups['sk'], $groups['rp'])
            );

        // Search ke banyak kolom (judul, nomor, kategori, tanggal)
        $query->when($r->filled('q'), function ($qq) use ($r) {
            $term = strtolower(trim($r->q));

            $qq->where(function ($q) use ($term) {
                // judul & nomor dokumen
                $q->whereRaw("LOWER(COALESCE(dokumen.judul, '')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("LOWER(COALESCE(dokumen.nomor_dokumen, '')) LIKE ?", ["%{$term}%"])
                  // nama kategori
                  ->orWhereRaw("LOWER(COALESCE(kategori.nama_kategori, '')) LIKE ?", ["%{$term}%"])
                  // tanggal upload (bulan, tahun, format lengkap, yyyy-mm-dd)
                  ->orWhereRaw("LOWER(to_char(dokumen.created_at, 'FMMonth')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("to_char(dokumen.created_at, 'YYYY') LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("to_char(dokumen.created_at, 'YYYY-MM-DD') LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("LOWER(to_char(dokumen.created_at, 'DD FMMonth YYYY')) LIKE ?", ["%{$term}%"]);
            });
        });

        // Filter dropdown
        if ($r->filled('cat') && isset($groups[$r->cat])) {
            $query->whereIn(
                DB::raw('LOWER(TRIM(kategori.nama_kategori))'),
                $groups[$r->cat]
            );
        }

        $docs = $query
            ->orderByDesc('dokumen.created_at')
            ->select([
                'dokumen.dokumen_id',
                'dokumen.judul',
                'dokumen.nomor_dokumen',
                'dokumen.created_at',
                'kategori.nama_kategori',
            ])
            ->paginate(10)
            ->withQueryString();

        // Variabel untuk view
        $kategories = Kategori::orderBy('nama_kategori')->get(['kategori_id', 'nama_kategori']);

        // Dosen penerima 
        $recipientsMap = [];
        if (Schema::hasTable('access_control') && $docs->total() > 0) {
            $rows = DB::table('access_control as ac')
                ->join('users as u', 'u.id_user', '=', 'ac.grantee_user_id')
                ->whereIn('ac.document_id', $docs->pluck('dokumen_id'))
                ->where('u.role', 'dosen')
                ->groupBy('ac.document_id')
                ->select('ac.document_id', DB::raw("string_agg(u.nama_lengkap, ', ' ORDER BY u.nama_lengkap) as names"))
                ->get();
            foreach ($rows as $r2) {
                $recipientsMap[$r2->document_id] = $r2->names;
            }
        }

        return view('tu.riwayat-upload', compact('docs', 'kategories', 'recipientsMap'));
    }

    // Halaman Detail Dokumen TU
    public function show($dokumen_id)
    {
        $dokumen = Dokumen::query()
            ->leftJoin('kategori', 'kategori.kategori_id', '=', 'dokumen.kategori_id')
            ->where('dokumen.dokumen_id', $dokumen_id)
            ->select('dokumen.*', 'kategori.nama_kategori')
            ->firstOrFail();

        $versi = VersiDokumen::where('dokumen_id', $dokumen_id)
            ->orderByDesc('nomor_versi')
            ->get();

        $latest = $versi->first();

        $recipients = [];
        if (Schema::hasTable('access_control')) {
            $recipients = DB::table('access_control as ac')
                ->join('users as u', 'u.id_user', '=', 'ac.grantee_user_id')
                ->where('ac.document_id', $dokumen_id)
                ->where('u.role', 'dosen')
                ->orderBy('u.nama_lengkap')
                ->pluck('u.nama_lengkap')
                ->toArray();
        }

        return view('tu.dokumen.show', compact('dokumen', 'versi', 'latest', 'recipients'));
    }
}
