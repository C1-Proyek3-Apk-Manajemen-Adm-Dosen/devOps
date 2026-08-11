<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    // =========================
    // ======= API LIST ========
    // =========================
    // GET /api/dokumen?q=&status=&kategori_id=&per_page=10
    public function index(Request $r)
    {
        $q = Dokumen::with(['kategori','creator'])
            ->when($r->filled('q'), fn($w) =>
                $w->where(function($x) use ($r) {
                    // PostgreSQL: ILIKE
                    $x->where('judul','ilike','%'.$r->q.'%')
                      ->orWhere('nomor_dokumen','ilike','%'.$r->q.'%');
                })
            )
            ->when($r->filled('status'), fn($w) => $w->where('status', $r->status))
            ->when($r->filled('kategori_id'), fn($w) => $w->where('kategori_id', $r->kategori_id))
            ->orderByDesc('dokumen_id');

        return $q->paginate($r->integer('per_page', 10));
    }

    // GET /api/dokumen/{id}
    public function show($id)
    {
        $doc = Dokumen::with(['kategori','creator','komentar.user','versi'])
            ->where('dokumen_id', $id)
            ->firstOrFail();

        return $doc;
    }

    // =========================
    // ====== WEB PAGES ========
    // =========================
    public function indexPage(Request $r)
    {
        // sesuaikan nama kolom kategori (di kamu: nama_kategori)
        $kategori = Kategori::select('kategori_id','nama_kategori')
            ->orderBy('nama_kategori')
            ->get();

        return view('dokumen.index', compact('kategori'));
    }

    // JSON untuk tabel di page (kalau perlu)
    public function indexJson(Request $r)
    {
        $q = Dokumen::with(['kategori','creator'])
            ->when($r->filled('q'), fn($w) =>
                $w->where(function($x) use ($r) {
                    $x->where('judul','ilike','%'.$r->q.'%')
                      ->orWhere('nomor_dokumen','ilike','%'.$r->q.'%');
                })
            )
            ->when($r->filled('status'), fn($w) => $w->where('status', $r->status))
            ->when($r->filled('kategori_id'), fn($w) => $w->where('kategori_id', $r->kategori_id))
            ->orderByDesc('dokumen_id');

        return response()->json($q->paginate($r->integer('per_page', 10)));
    }

    // =========================
    // ====== CRUD FILES =======
    // =========================

    /**
     * Upload file ke MinIO + simpan meta ke DB.
     * Expect form fields:
     * - judul_dokumen (fallback ke 'judul' kalau tidak ada)
     * - file_upload (fallback ke 'file' kalau tidak ada)
     * - (opsional) kategori_id, nomor_dokumen, tanggal_terbit, deskripsi, status
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'judul_dokumen' => 'nullable|string|max:255',
                'judul'         => 'nullable|string|max:255',
                'file_upload'   => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
                'file'          => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
                'kategori_id'   => 'nullable|integer|exists:kategori,kategori_id',
                'nomor_dokumen' => 'nullable|string|max:100',
                'tanggal_terbit'=> 'nullable|date',
                'deskripsi'     => 'nullable|string',
                'status'        => 'nullable|string|max:50',
            ]);

            // Ambil input judul dgn fallback
            $judul = $request->input('judul_dokumen') ?? $request->input('judul');
            if (!$judul) {
                return back()->withErrors(['judul_dokumen' => 'Judul dokumen wajib diisi.']);
            }

            // Ambil file dgn fallback
            $file = $request->file('file_upload') ?? $request->file('file');
            if (!$file) {
                return back()->withErrors(['file_upload' => 'File wajib diunggah.']);
            }

            // Folder tujuan dalam bucket
            $folderPath = 'dokumen-uploads';

            // Nama file aman + unik
            $originalName = $file->getClientOriginalName();
            $base = pathinfo($originalName, PATHINFO_FILENAME);
            $ext  = $file->getClientOriginalExtension();
            $safe = \Illuminate\Support\Str::slug($base, '-');
            $uniqueFileName = now()->format('YmdHis').'-'.\Illuminate\Support\Str::random(6).'-'.$safe.($ext ? '.'.$ext : '');

            // Simpan ke MinIO
            $path = \Illuminate\Support\Facades\Storage::disk('minio')->putFileAs($folderPath, $file, $uniqueFileName);

            // Simpan ke database
            \App\Models\Dokumen::create([
                'judul'          => $judul,
                'nomor_dokumen'  => $request->input('nomor_dokumen'),
                'tanggal_terbit' => $request->input('tanggal_terbit'),
                'kategori_id'    => $request->input('kategori_id'),
                'file_path'      => $path, // contoh: dokumen-uploads/20251110-abc123-judul.pdf
                'deskripsi'      => $request->input('deskripsi'),
                'created_by'     => \Illuminate\Support\Facades\Auth::id(),
                'status'         => $request->input('status', 'draft'),
            ]);

            // Notifikasi sukses
            return back()->with('success', 'Dokumen berhasil di-upload ke MinIO dan data tersimpan!');
        } catch (\Throwable $e) {
            // Kalau error (upload gagal, DB error, dll)
            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }


    /**
     * Update metadata dokumen + opsi ganti file.
     * Expect form fields opsional:
     * - judul, nomor_dokumen, tanggal_terbit, kategori_id, deskripsi, status
     * - file_upload/file (opsional) untuk ganti file
     */
    public function update(Request $request, $id)
    {
        $dokumen = Dokumen::findOrFail($id);

        $request->validate([
            'judul'         => 'nullable|string|max:255',
            'nomor_dokumen' => 'nullable|string|max:100',
            'tanggal_terbit'=> 'nullable|date',
            'kategori_id'   => 'nullable|integer|exists:kategori,kategori_id',
            'deskripsi'     => 'nullable|string',
            'status'        => 'nullable|string|max:50',
            'file_upload'   => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'file'          => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        $dokumen->fill($request->only([
            'judul','nomor_dokumen','tanggal_terbit','kategori_id','deskripsi','status'
        ]));

        // Kalau ada file baru -> hapus lama, upload baru
        $newFile = $request->file('file_upload') ?? $request->file('file');
        if ($newFile) {
            // hapus lama (ignore kalau ga ada)
            if ($dokumen->file_path && Storage::disk('minio')->exists($dokumen->file_path)) {
                Storage::disk('minio')->delete($dokumen->file_path);
            }

            $folderPath = 'dokumen-uploads';
            $originalName = $newFile->getClientOriginalName();
            $base = pathinfo($originalName, PATHINFO_FILENAME);
            $ext  = $newFile->getClientOriginalExtension();
            $safe = Str::slug($base, '-');
            $uniqueFileName = now()->format('YmdHis').'-'.Str::random(6).'-'.$safe.($ext ? '.'.$ext : '');

            $path = Storage::disk('minio')->putFileAs($folderPath, $newFile, $uniqueFileName);
            $dokumen->file_path = $path;
        }

        $dokumen->save();

        return back()->with('success', 'Dokumen berhasil diperbarui!');
    }

    /**
     * Hapus record + file di MinIO.
     */
    public function destroy($id)
    {
        $dokumen = Dokumen::findOrFail($id);

        if ($dokumen->file_path && Storage::disk('minio')->exists($dokumen->file_path)) {
            Storage::disk('minio')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        return back()->with('success', 'Dokumen berhasil dihapus!');
    }

    // =========================
    // ====== UTILITIES ========
    // =========================

    /**
     * Redirect langsung ke URL publik MinIO (tanpa login).
     * Route contoh: GET /dokumen/{id}/open
     */
    public function open($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        if (!$dokumen->file_path) abort(404, 'File tidak ditemukan.');

        return redirect(Storage::disk('minio')->url($dokumen->file_path));
    }

    /**
     * Ambil URL publik dalam bentuk JSON.
     * Route contoh: GET /api/dokumen/{id}/url
     */
    public function url($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        if (!$dokumen->file_path) abort(404, 'File tidak ditemukan.');

        return response()->json([
            'url' => Storage::disk('minio')->url($dokumen->file_path),
        ]);
    }
}
