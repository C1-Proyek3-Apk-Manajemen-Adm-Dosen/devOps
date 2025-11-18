<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;
use App\Models\AccessControl; // Import AccessControl
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Import DB

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
        $kategori = Kategori::orderBy('nama_kategori')->get();
        return view('tu.dokumen-list', compact('kategori'));
    }

    public function indexJson(Request $r)
    {
        $q = Dokumen::with(['kategori', 'creator'])
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

    public function create()
    {
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $userList = User::where('role', '!=', 'admin')->orderBy('nama')->get(); 
        return view('tu.upload-dokumen', compact('kategoriList', 'userList'));
    }


    // =========================
    // ====== CRUD FILES =======
    // =========================
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'judul'           => 'required|string|max:255',
            'nomor_dokumen'   => 'nullable|string|max:100',
            'tanggal_terbit'  => ['required', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'], // Format d/m/Y
            'kategori_id'     => 'required|exists:kategori,kategori_id',
            'deskripsi'       => 'required|string',
            'owner_user_id'   => 'required|array|min:1', // Ini adalah "Hak Akses" (array)
            'owner_user_id.*' => 'exists:users,id_user', // Pastikan setiap ID ada
            'file'            => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480', // max 20MB
        ], [
            'owner_user_id.required' => 'Minimal pilih 1 pengguna untuk hak akses.',
            'owner_user_id.min'      => 'Minimal pilih 1 pengguna untuk hak akses.',
        ]);

        // Convert tanggal
        try {
            $tanggal = \DateTime::createFromFormat('d/m/Y', $request->tanggal_terbit);
            $tanggalFormatted = $tanggal ? $tanggal->format('Y-m-d H:i:s') : null;
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Format tanggal terbit tidak valid.');
        }

        // Gunakan DB Transaction untuk memastikan data konsisten
        DB::beginTransaction();
        try {
            // 1. Upload file ke MinIO
            $folderPath = 'dokumen-uploads';
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $base = pathinfo($originalName, PATHINFO_FILENAME);
            $ext  = $file->getClientOriginalExtension();
            $safe = Str::slug($base, '-');
            $uniqueFileName = now()->format('YmdHis') . '-' . Str::random(6) . '-' . $safe . '.' . $ext;
            
            // Simpan file
            $path = Storage::disk('minio')->putFileAs($folderPath, $file, $uniqueFileName);

            // 2. Simpan Dokumen
            $dokumen = Dokumen::create([
                'judul'          => $request->judul,
                'nomor_dokumen'  => $request->nomor_dokumen,
                'tanggal_terbit' => $tanggalFormatted,
                'kategori_id'    => $request->kategori_id,
                'deskripsi'      => $request->deskripsi,
                'file_path'      => $path,
                'created_by'     => Auth::id(),
                'status'         => 'draft', 
                'owner_user_id'  => Auth::id(), // Diisi ID integer peng-upload
            ]);

            // 3. Simpan Hak Akses ke tabel 'access_control' (BARU)
            $usersWithAccess = $request->owner_user_id;
            $accessControlData = [];
            $uploaderId = Auth::id(); // ID si peng-upload

            foreach ($usersWithAccess as $userId) {
                $accessControlData[] = [
                    'document_id'     => $dokumen->dokumen_id, // ID dari dokumen yg baru dibuat
                    'grantee_user_id' => $userId,
                    'perm'            => 'READ', 
                    'status'          => 'PENDING', // <-- INI PERBAIKANNYA
                    'created_at'      => now(),
                    'created_by'      => $uploaderId,
                ];
            }

            // Insert semua hak akses sekaligus
            AccessControl::insert($accessControlData); 

            // Jika semua sukses, commit
            DB::commit();

            return back()->with('success', 'Dokumen berhasil diupload!');

        } catch (\Throwable $e) {
            // Jika ada error, rollback semua
            DB::rollBack();
            
            if (isset($path) && Storage::disk('minio')->exists($path)) {
                Storage::disk('minio')->delete($path);
            }
            
            \Log::error('Upload Dokumen Gagal: ' . $e->getMessage()); 
            return back()->withInput()->with('error', 'Upload gagal: Terjadi kesalahan. ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $userList = User::where('role', '!=', 'admin')->orderBy('nama')->get();

        $currentAccess = AccessControl::where('document_id', $id)
                                      ->pluck('grantee_user_id')
                                      ->toArray();

        return view('tu.edit-dokumen', compact('dokumen', 'kategoriList', 'userList', 'currentAccess'));
    }

    public function update(Request $request, $id)
    {
        $dokumen = Dokumen::findOrFail($id);

        $request->validate([
            'judul'           => 'required|string|max:255',
            'nomor_dokumen'   => 'nullable|string|max:100',
            'tanggal_terbit'  => ['required', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
            'kategori_id'     => 'required|exists:kategori,kategori_id',
            'deskripsi'       => 'required|string',
            'owner_user_id'   => 'required|array|min:1', // Hak Akses
            'owner_user_id.*' => 'exists:users,id_user',
            'file'            => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480',
            'status'          => 'required|in:draft,published,archived', 
        ], [
            'owner_user_id.required' => 'Minimal pilih 1 pengguna untuk hak akses.',
            'owner_user_id.min'      => 'Minimal pilih 1 pengguna untuk hak akses.',
        ]);

        // Convert tanggal
        try {
            $tanggal = \DateTime::createFromFormat('d/m/Y', $request->tanggal_terbit);
            $tanggalFormatted = $tanggal ? $tanggal->format('Y-m-d H:i:s') : null;
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Format tanggal terbit tidak valid.');
        }

        DB::beginTransaction();
        try {
            // 1. Update data utama dokumen
            $dokumen->fill([
                'judul'          => $request->judul,
                'nomor_dokumen'  => $request->nomor_dokumen,
                'tanggal_terbit' => $tanggalFormatted,
                'kategori_id'    => $request->kategori_id,
                'deskripsi'      => $request->deskripsi,
                'status'         => $request->status,
            ]);

            // 2. Cek jika ada file baru
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

            // 3. Update Hak Akses di tabel 'access_control'
            if ($request->filled('owner_user_id')) {
                // 3a. Hapus semua hak akses lama
                AccessControl::where('document_id', $dokumen->dokumen_id)->delete();

                // 3b. Buat ulang hak akses
                $usersWithAccess = $request->owner_user_id;
                $accessControlData = [];
                $updaterId = Auth::id(); 

                foreach ($usersWithAccess as $userId) {
                    $accessControlData[] = [
                        'document_id'     => $dokumen->dokumen_id,
                        'grantee_user_id' => $userId,
                        'perm'            => 'READ', 
                        'status'          => 'PENDING', // <-- INI PERBAIKANNYA
                        'created_at'      => now(),
                        'created_by'      => $updaterId,
                    ];
                }
                
                AccessControl::insert($accessControlData);
            }

            DB::commit();
            return redirect()->route('tu.dokumen.list')->with('success', 'Dokumen berhasil diperbarui!');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Update Dokumen Gagal: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Update gagal: Terjadi kesalahan. ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $dokumen = Dokumen::findOrFail($id);

            if ($dokumen->file_path && Storage::disk('minio')->exists($dokumen->file_path)) {
                Storage::disk('minio')->delete($dokumen->file_path);
            }

            AccessControl::where('document_id', $dokumen->dokumen_id)->delete();
            
            // Hapus relasi lain jika ada
            // $dokumen->komentar()->delete();
            // $dokumen->versi()->delete();

            $dokumen->delete();

            DB::commit();
            return back()->with('success', 'Dokumen berhasil dihapus!');
            
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Hapus Dokumen Gagal: ' . $e->getMessage());
            return back()->with('error', 'Hapus gagal: ' . $e->getMessage());
        }
    }

    // =========================
    // ====== UTILITIES ========
    // =========================
    public function open($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        if (!$dokumen->file_path) abort(404, 'File tidak ditemukan.');

        // TODO: Cek hak akses
        
        try {
            $url = Storage::disk('minio')->temporaryUrl(
                $dokumen->file_path,
                now()->addMinutes(10)
            );
        } catch (\Exception $e) {
            \Log::warning("Gagal generate temporary URL: " . $e->getMessage());
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

        // TODO: Cek hak akses

        try {
            $url = Storage::disk('minio')->temporaryUrl(
                $dokumen->file_path,
                now()->addMinutes(10)
            );
        } catch (\Exception $e) {
            \Log::warning("Gagal generate temporary URL (API): " . $e->getMessage());
            $endpoint = config('filesystems.disks.minio.endpoint');
            $bucket = config('filesystems.disks.minio.bucket');
            $url = rtrim($endpoint, '/') . '/' . $bucket . '/' . ltrim($dokumen->file_path, '/');
        }

        return response()->json(['url' => $url]);
    }
}