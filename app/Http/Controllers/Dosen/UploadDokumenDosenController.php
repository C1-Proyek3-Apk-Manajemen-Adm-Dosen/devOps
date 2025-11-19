<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// SESUAIKAN NAMESPACE MODEL DENGAN PUNYAMU
use App\Models\Dokumen;
use App\Models\AccessControl; // Pastikan model ini ada dan table = 'access_control_entries'
use App\Models\User;
use App\Models\Kategori;

class UploadDokumenDosenController extends Controller
{
    /**
     * Tampilkan form upload dokumen untuk dosen.
     * View: resources/views/dosen/upload.blade.php
     */
    public function create()
    {
        // Ambil daftar user yang bisa diberi hak akses
        // SESUAIKAN: Query sama dengan TU (pakai selectRaw untuk mapping kolom)
        $users = User::selectRaw('id_user as id, nama_lengkap as name, email')
            ->orderBy('nama_lengkap')
            ->get();
        
        // Ambil kategori (gunakan DB::table untuk bypass model)
        $kategoris = DB::table('kategori') // ✅ Nama tabel: kategori (tanpa 's')
            ->select('kategori_id', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();

        return view('dosen.upload', compact('users', 'kategoris'));
    }

    /**
     * ALTERNATIF: Jika route Anda menggunakan method index() atau upload()
     * Gunakan salah satu dari method di bawah ini:
     */
    
    // Jika route menggunakan index()
    public function index()
    {
        $users = User::selectRaw('id_user as id, nama_lengkap as name, email')
            ->orderBy('nama_lengkap')
            ->get();
        $kategoris = Kategori::select('kategori_id', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();
        return view('dosen.upload', compact('users', 'kategoris'));
    }
    
    // Jika route menggunakan upload() 
    public function upload()
    {
        $users = User::selectRaw('id_user as id, nama_lengkap as name, email')
            ->orderBy('nama_lengkap')
            ->get();
        $kategoris = Kategori::select('kategori_id', 'nama_kategori')
            ->orderBy('nama_kategori')
            ->get();
        return view('dosen.upload', compact('users', 'kategoris'));
    }

    /**
     * Proses penyimpanan dokumen yang diupload dosen.
     */
    public function store(Request $request) // ✅ Pakai Request biasa, bukan FormRequest
    {
        // Validasi input (UPDATED - tambah field baru)
        $validated = $request->validate([
            'judul'            => ['required', 'string', 'max:255'],
            'nomor_dokumen'    => ['nullable', 'string', 'max:100'],
            'tanggal_terbit'   => ['required', 'string'], // flatpickr kirim d/m/Y
            'kategori_id'      => ['required', 'integer'], // ✅ Hapus exists:kategoris
            'deskripsi'        => ['required', 'string'],
            'owner_user_id'    => ['required', 'array', 'min:1'],
            'owner_user_id.*'  => ['integer'], // ✅ Hapus exists:users
            'file'             => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
        ], [
            'judul.required'           => 'Judul dokumen wajib diisi.',
            'nomor_dokumen.max'        => 'Nomor dokumen maksimal 100 karakter.',
            'tanggal_terbit.required'  => 'Tanggal terbit wajib diisi.',
            'kategori_id.required'     => 'Kategori dokumen wajib dipilih.',
            'kategori_id.integer'      => 'Kategori yang dipilih tidak valid.',
            'deskripsi.required'       => 'Deskripsi dokumen wajib diisi.',
            'owner_user_id.required'   => 'Pilih minimal satu pengguna yang dapat mengakses.',
            'owner_user_id.array'      => 'Format hak akses tidak sesuai.',
            'owner_user_id.min'        => 'Pilih minimal satu pengguna yang dapat mengakses.',
            'file.required'            => 'File dokumen wajib diunggah.',
            'file.max'                 => 'Ukuran file maksimal 20MB.',
            'file.mimes'               => 'Format file harus pdf, doc, docx, xls, xlsx, jpg, jpeg, atau png.',
        ]);

        // Manual validation untuk kategori_id (bypass exists:kategoris)
        $kategoriExists = DB::table('kategori')->where('kategori_id', $validated['kategori_id'])->exists();
        if (!$kategoriExists) {
            return back()->withErrors(['kategori_id' => 'Kategori yang dipilih tidak valid.'])->withInput();
        }

        // Manual validation untuk owner_user_id (bypass exists:users)
        foreach ($validated['owner_user_id'] as $userId) {
            $userExists = DB::table('users')->where('id_user', $userId)->exists();
            if (!$userExists) {
                return back()->withErrors(['owner_user_id' => 'Data pengguna tidak valid.'])->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // Ubah format tanggal dari d/m/Y ke Y-m-d
            $tanggalTerbit = Carbon::createFromFormat('d/m/Y', $validated['tanggal_terbit'])->format('Y-m-d');

            // Simpan file ke storage (disk: minio, sesuai dengan Model Dokumen)
            $filePath = $request->file('file')->store('dokumen/dosen', 'minio');

            // === BUAT RECORD DOKUMEN ===
            // Status tidak di-set, biar pakai default database ('pending')
            $dokumen = Dokumen::create([
                'judul'          => $validated['judul'],
                'nomor_dokumen'  => $validated['nomor_dokumen'],
                'tanggal_terbit' => $tanggalTerbit,
                'kategori_id'    => $validated['kategori_id'],
                'deskripsi'      => $validated['deskripsi'],
                'file_path'      => $filePath,
                'created_by'     => Auth::id(),
                'owner_user_id'  => Auth::id(),
            ]);

            // === BUAT HAK AKSES ===
            // Gunakan nilai yang valid sesuai constraint database
            $hakAksesUserIds = $validated['owner_user_id'];

            foreach ($hakAksesUserIds as $userId) {
                AccessControl::create([
                    'document_id'      => $dokumen->dokumen_id,
                    'grantee_user_id'  => $userId,
                    'perm'             => 'READ', // ✅ UPPERCASE!
                    'status'           => 'CONFIRMED', // ✅ UPPERCASE!
                    'created_by'       => Auth::id(),
                    'created_at'       => now(),
                ]);
            }

            // Pastikan uploader juga punya hak akses (kalau belum masuk list)
            if (!in_array(Auth::id(), $hakAksesUserIds)) {
                AccessControl::create([
                    'document_id'      => $dokumen->dokumen_id,
                    'grantee_user_id'  => Auth::id(),
                    'perm'             => 'OWNER', // ✅ Uploader adalah OWNER
                    'status'           => 'CONFIRMED', // ✅ UPPERCASE!
                    'created_by'       => Auth::id(),
                    'created_at'       => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('dosen.upload')
                ->with('success', 'Dokumen berhasil diunggah.');
        } catch (\Throwable $e) {
            // Kalau mau log ke file:
            \Log::error('Gagal upload dokumen dosen', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kesalahan saat menyimpan dokumen.']);
        }
    }
}