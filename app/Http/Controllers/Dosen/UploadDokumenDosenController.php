<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// Model
use App\Models\Dokumen;
use App\Models\AccessControl;
use App\Models\User;
use App\Models\Kategori;
use App\Models\VersiDokumen;

class UploadDokumenDosenController extends Controller
{
    /**
     * Tampilkan halaman upload dokumen untuk DOSEN.
     */
    public function create()
    {
        // Ambil user untuk hak akses
        $users = User::selectRaw('id_user as id, nama_lengkap as name, email')
            ->orderBy('nama_lengkap')
            ->get();

        // Kategori khusus DOSEN
        $kategoris = DB::table('kategori')
            ->select('kategori_id', 'nama_kategori')
            ->whereIn('nama_kategori', [
                'RPS',
                'BKD',
                'SKP',
                'Bukti Pengajaran'
            ])
            ->orderBy('nama_kategori')
            ->get();

        return view('dosen.upload', compact('users', 'kategoris'));
    }

    /**
     * Proses penyimpanan dokumen yang di-upload oleh DOSEN.
     * Sekaligus:
     * - Cek duplikat judul
     * - Buat versi pertama di versi_dokumen
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'judul' => [
                'required',
                'string',
                'max:255',
                // Custom validation untuk cek duplicate judul
                function ($attribute, $value, $fail) {
                    // Cek duplicate berdasarkan judul DAN user yang upload
                    $exists = Dokumen::where('judul', $value)
                        ->where('created_by', auth()->id())
                        ->exists();
                    
                    if ($exists) {
                        $fail('Dokumen dengan judul "' . $value . '" sudah ada. Silakan gunakan judul yang berbeda.');
                    }
                },
            ],
            'nomor_dokumen' => 'nullable|string|max:100',
            'tanggal_terbit' => 'required|date_format:d/m/Y',
            'kategori_id' => 'required|exists:kategori,kategori_id',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480',
            'deskripsi' => 'required|string',
            'owner_user_id' => 'required|array|min:1',
            'owner_user_id.*' => 'exists:users,id_user',
        ]);

        // ============================================================
        // 1️⃣ CEK DUPLIKAT BERDASARKAN JUDUL
        // ============================================================
        $existingDoc = Dokumen::where('judul', $validated['judul'])->first();

        if ($existingDoc) {
            // TIDAK upload file, TIDAK buat dokumen.
            // Kita cuma kirim flag ke view supaya JS bisa munculin modal.
            return back()
                ->withInput()
                ->with('duplicate', true)
                ->with('duplicate_dokumen_id', $existingDoc->dokumen_id);
        }

        // Pastikan kategori valid (khusus kategori dosen)
        $kategoriExists = DB::table('kategori')
            ->where('kategori_id', $validated['kategori_id'])
            ->whereIn('nama_kategori', ['RPS', 'BKD', 'SKP', 'Bukti Pengajaran'])
            ->exists();

        if (!$kategoriExists) {
            return back()->withErrors(['kategori_id' => 'Kategori tidak valid.'])->withInput();
        }

        // Validasi owner_user_id
        foreach ($validated['owner_user_id'] as $userId) {
            $exists = DB::table('users')->where('id_user', $userId)->exists();
            if (!$exists) {
                return back()->withErrors(['owner_user_id' => 'Data pengguna tidak valid.'])->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // Format tanggal
            $tanggalTerbit = Carbon::createFromFormat('d/m/Y', $validated['tanggal_terbit'])
                ->format('Y-m-d');

            // ============================================================
            // 2️⃣ UPLOAD FILE KE MINIO (PAKAI NAMA RANDOM)
            // ============================================================
            $file       = $request->file('file');
            $randomName = uniqid() . '_' . $file->getClientOriginalName();
            $filePath   = 'dokumen/dosen/' . $randomName;

            Storage::disk('minio')->put($filePath, file_get_contents($file));

            // ============================================================
            // 3️⃣ BUAT RECORD DOKUMEN (PERSIS SEPERTI KODE AWALMU)
            // ============================================================
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

            // ============================================================
            // 4️⃣ BUAT VERSI PERTAMA DI TABEL versi_dokumen
            // ============================================================
            VersiDokumen::create([
                'dokumen_id'      => $dokumen->dokumen_id,
                'nomor_versi'     => 1,
                'file_path'       => $filePath,
                'tanggal_dokumen' => now(),
                'upload_by'       => Auth::id(),
            ]);

            // ============================================================
            // 5️⃣ BERIKAN HAK AKSES READ
            // ============================================================
            foreach ($validated['owner_user_id'] as $userId) {
                AccessControl::create([
                    'document_id'      => $dokumen->dokumen_id,
                    'grantee_user_id'  => $userId,
                    'perm'             => 'READ',
                    'status'           => 'CONFIRMED',
                    'created_by'       => Auth::id(),
                    'created_at'       => now(),
                ]);
            }

            // Pastikan uploader punya akses OWNER
            if (!in_array(Auth::id(), $validated['owner_user_id'])) {
                AccessControl::create([
                    'document_id'      => $dokumen->dokumen_id,
                    'grantee_user_id'  => Auth::id(),
                    'perm'             => 'OWNER',
                    'status'           => 'CONFIRMED',
                    'created_by'       => Auth::id(),
                    'created_at'       => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('dosen.upload')
                ->with('success', 'Dokumen berhasil diunggah.');

        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Gagal upload dokumen dosen', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kesalahan saat menyimpan dokumen.']);
        }
    }
}
