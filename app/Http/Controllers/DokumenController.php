<?php

namespace App\Http\Controllers;

use App\Models\Dokumen; // <-- Pastikan ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Pastikan ini ada
use Illuminate\Support\Facades\Auth; // <-- TAMBAHAN: Untuk mengambil user ID

class DokumenController extends Controller
{
    // GET /api/dokumen?q=&status=&kategori_id=
    public function index(Request $r)
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

        return $q->paginate(10);
    }

    // GET /api/dokumen/{id}
    public function show($id)
    {
        $doc = Dokumen::with(['kategori','creator','komentar.user','versi'])
            ->where('dokumen_id', $id)
            ->firstOrFail();

        return $doc;
    }

    public function indexPage(\Illuminate\Http\Request $r)
    {
        $kategori = \App\Models\Kategori::select('kategori_id','nama_kategori')
            ->orderBy('nama_kategori')->get();

        return view('dokumen.index', compact('kategori'));
    }

    public function indexJson(\Illuminate\Http\Request $r)
    {
        $q = \App\Models\Dokumen::with(['kategori','creator'])
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

    // =======================================================
    // == METHOD UPLOAD YANG SUDAH DIPERBARUI ==
    // =======================================================

    /**
     * Menyimpan file yang di-upload ke MinIO dan infonya ke DB.
     */
    public function store(Request $request)
    {
        // 1. Validasi request
        $request->validate([
            'judul_dokumen' => 'required|string|max:255',
            'file_upload' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:10240', // Maks 10MB
        ]);

        // 2. Ambil file dari request
        $file = $request->file('file_upload');
        
        // 3. Buat nama file yang unik
        $originalName = $file->getClientOriginalName();
        $uniqueFileName = time() . '_' . $originalName;
        
        // 4. Tentukan folder di dalam bucket
        $folderPath = 'dokumen-uploads';

        // 5. Simpan file ke MinIO
        $path = $file->storeAs(
            $folderPath,      // Folder (misal: 'dokumen-uploads')
            $uniqueFileName,  // Nama file unik kita
            'minio'           // Nama disk dari 'config/filesystems.php'
        );

        // 6. (AKTIF) Simpan ke Database PostgreSQL
        // Disesuaikan dengan skema db_sidora_v5.sql
        
        Dokumen::create([
             'judul' => $request->judul_dokumen,
             'file_path' => $path, // Ini adalah path dari MinIO: "dokumen-uploads/1678888_myfile.pdf"
             'created_by' => Auth::id(), // Mengambil ID user yang sedang login
             'owner_user_id' => Auth::id(), // Set owner ke user yang upload
             'status' => 'draft', // Status default
             
             // 'kategori_id' => $request->kategori_id, // (Bisa ditambahkan jika form-nya diupdate)
             // 'deskripsi' => $request->deskripsi, // (Bisa ditambahkan jika form-nya diupdate)
        ]);
        

        // 7. Kembalikan ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'File berhasil di-upload ke MinIO dan data tersimpan di Database!');
    }
}