<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\VersiDokumen;

class RiwayatUploadController extends Controller
{
    // ======================
    // LIST RIWAYAT DOSEN
    // ======================
    public function index(Request $request)
    {
        $uid = auth()->id();

        $query = Dokumen::query()
            ->leftJoin('kategori', 'kategori.kategori_id', '=', 'dokumen.kategori_id')
            ->where('dokumen.created_by', $uid);

        // SEARCH
        if ($request->filled('q')) {
            $term = strtolower(trim($request->q));

            $query->where(function ($q) use ($term) {
                $q->whereRaw("LOWER(COALESCE(dokumen.judul, '')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("LOWER(COALESCE(dokumen.nomor_dokumen, '')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("LOWER(COALESCE(kategori.nama_kategori, '')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("LOWER(to_char(dokumen.created_at, 'DD FMMonth YYYY')) LIKE ?", ["%{$term}%"])
                  ->orWhereRaw("to_char(dokumen.created_at, 'YYYY') LIKE ?", ["%{$term}%"]);
            });
        }

        // FILTER KATEGORI
        if ($request->filled('kategori_id') && $request->kategori_id !== 'all') {
            $query->where('dokumen.kategori_id', $request->kategori_id);
        }

        // FILTER PERIODE
        if ($request->filled('period') && $request->period !== 'all') {
            $query->where('dokumen.created_at', '>=', now()->subDays((int) $request->period));
        }

        // PAGINATE 5 ITEMS
        $docs = $query
            ->orderByDesc('dokumen.created_at')
            ->select([
                'dokumen.dokumen_id',
                'dokumen.judul',
                'dokumen.nomor_dokumen',
                'dokumen.created_at',
                'kategori.nama_kategori',
            ])
            ->paginate(5)
            ->withQueryString();

        $kategories = Kategori::orderBy('nama_kategori')->get();

        return view('dosen.riwayat', compact('docs', 'kategories'));
    }

    // ======================
    // DETAIL
    // ======================
    public function show($dokumen_id)
    {
        $uid = auth()->id();

        $dokumen = Dokumen::with(['kategori', 'versi'])
            ->where('dokumen_id', $dokumen_id)
            ->where('created_by', $uid)
            ->firstOrFail();

        $versi = $dokumen->versi->sortByDesc('nomor_versi')->values();
        $latest = $versi->first();

        // PENTING: gunakan view ('dosen.show')
        return view('dosen.dokumen.show', [
            'dokumen' => $dokumen,
            'latest'  => $latest,
            'versi'   => $versi,
        ]);
    }
}
