<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    // =========================
    // ======= API LIST ========
    // =========================
    public function index(Request $r)
    {
        $q = Dokumen::with(['kategori', 'owner'])
            ->when($r->filled('q'), fn($w) =>
                $w->where(function ($x) use ($r) {
                    $x->where('judul', 'ilike', '%' . $r->q . '%')
                      ->orWhere('nomor_dokumen', 'ilike', '%' . $r->q . '%');
                })
            )
            ->when($r->filled('status'), fn($w) => $w->where('status', $r->status))
            ->when($r->filled('kategori_id'), fn($w) => $w->where('kategori_id', $r->kategori_id))
            ->orderByDesc('dokumen_id');

        return $q->paginate($r->integer('per_page', 10));
    }

    public function show($id)
    {
        $doc = Dokumen::with(['kategori', 'owner', 'komentar.user', 'versi'])
            ->where('dokumen_id', $id)
            ->firstOrFail();

        return $doc;
    }

    // =========================
    // ====== WEB PAGES ========
    // =========================
    public function indexPage(Request $r)
    {
        $kategori = Kategori::select('kategori_id', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();

        return view('dokumen.index', compact('kategori'));
    }

    public function indexJson(Request $r)
    {
        $q = Dokumen::with(['kategori', 'owner'])
            ->when($r->filled('q'), fn($w) =>
                $w->where(function ($x) use ($r) {
                    $x->where('judul', 'ilike', '%' . $r->q . '%')
                      ->orWhere('nomor_dokumen', 'ilike', '%' . $r->q . '%');
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
    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul'           => 'required|string|max:255',
                'nomor_dokumen'   => 'nullable|string|max:100',
                'tanggal_terbit'  => ['required', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
                'kategori_id'     => 'required|exists:kategori,kategori_id',
                'deskripsi'       => 'required|string',
                'owner_user_id'   => 'required|array',
                'owner_user_id.*' => 'exists:users,id_user',
                'file'            => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480',
            ]);

            // Tambahkan ini di sini, sebelum upload file ⬇
            // dd($request->all());

            // Convert tanggal dd/mm/yyyy → yyyy-mm-dd
            $tanggal = \DateTime::createFromFormat('d/m/Y', $request->tanggal_terbit);
            $tanggalFormatted = $tanggal ? $tanggal->format('Y-m-d') : null;

            // Upload ke MinIO
            $folderPath = 'dokumen-uploads';
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $base = pathinfo($originalName, PATHINFO_FILENAME);
            $ext  = $file->getClientOriginalExtension();
            $safe = Str::slug($base, '-');
            $uniqueFileName = now()->format('YmdHis') . '-' . Str::random(6) . '-' . $safe . '.' . $ext;

            $path = Storage::disk('minio')->putFileAs($folderPath, $file, $uniqueFileName);

            // Simpan ke DB (owner_user_id jadi JSON)
            Dokumen::create([
                'judul'          => $request->judul,
                'nomor_dokumen'  => $request->nomor_dokumen,
                'tanggal_terbit' => $tanggalFormatted,
                'kategori_id'    => $request->kategori_id,
                'deskripsi'      => $request->deskripsi,
                'owner_user_id'  => json_encode($request->owner_user_id), // <== WAJIB
                'file_path'      => $path,
                'created_by'     => Auth::id(),
                'status'         => 'draft',
            ]);

            return back()->with('success', 'Dokumen berhasil diupload!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Upload gagal: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $dokumen = Dokumen::findOrFail($id);

        $request->validate([
            'judul'           => 'nullable|string|max:255',
            'nomor_dokumen'   => 'nullable|string|max:100',
            'tanggal_terbit'  => 'nullable|date',
            'kategori_id'     => 'nullable|integer|exists:kategori,kategori_id',
            'deskripsi'       => 'nullable|string',
            'status'          => 'nullable|string|max:50',
            'owner_user_id'   => 'nullable|array',
            'owner_user_id.*' => 'exists:users,id_user',
            'file'            => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480',
        ]);

        // Ganti field utama
        $dokumen->fill($request->only([
            'judul', 'nomor_dokumen', 'tanggal_terbit', 'kategori_id', 'deskripsi', 'status'
        ]));

        // Simpan ulang owner (kalau dikirim)
        if ($request->filled('owner_user_id')) {
            $dokumen->owner_user_id = json_encode($request->owner_user_id);
        }

        // Ganti file jika ada
        if ($request->hasFile('file')) {
            if ($dokumen->file_path && Storage::disk('minio')->exists($dokumen->file_path)) {
                Storage::disk('minio')->delete($dokumen->file_path);
            }

            $folderPath = 'dokumen-uploads';
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $base = pathinfo($originalName, PATHINFO_FILENAME);
            $ext  = $file->getClientOriginalExtension();
            $safe = Str::slug($base, '-');
            $uniqueFileName = now()->format('YmdHis') . '-' . Str::random(6) . '-' . $safe . '.' . $ext;

            $path = Storage::disk('minio')->putFileAs($folderPath, $file, $uniqueFileName);
            $dokumen->file_path = $path;
        }

        $dokumen->save();

        return back()->with('success', 'Dokumen berhasil diperbarui!');
    }

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
    public function open($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        if (!$dokumen->file_path) abort(404, 'File tidak ditemukan.');

        try {
            $url = Storage::disk('minio')->temporaryUrl(
                $dokumen->file_path,
                now()->addMinutes(10)
            );
        } catch (\Exception $e) {
            $endpoint = config('filesystems.disks.minio.endpoint');
            $bucket = config('filesystems.disks.minio.bucket');
            $url = rtrim($endpoint, '/') . '/' . $bucket . '/' . ltrim($dokumen->file_path, '/');
        }

        return redirect($url);
    }

    public function url($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        if (!$dokumen->file_path) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }

        try {
            $url = Storage::disk('minio')->temporaryUrl(
                $dokumen->file_path,
                now()->addMinutes(10)
            );
        } catch (\Exception $e) {
            $endpoint = config('filesystems.disks.minio.endpoint');
            $bucket = config('filesystems.disks.minio.bucket');
            $url = rtrim($endpoint, '/') . '/' . $bucket . '/' . ltrim($dokumen->file_path, '/');
        }

        return response()->json(['url' => $url]);
    }
}
