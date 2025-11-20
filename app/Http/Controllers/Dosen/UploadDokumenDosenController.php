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
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'judul'            => ['required', 'string', 'max:255'],
            'nomor_dokumen'    => ['nullable', 'string', 'max:100'],
            'tanggal_terbit'   => ['required', 'string'], // flatpickr -> d/m/Y
            'kategori_id'      => ['required', 'integer'],
            'deskripsi'        => ['required', 'string'],
            'owner_user_id'    => ['required', 'array', 'min:1'],
            'owner_user_id.*'  => ['integer'],
            'file'             => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
        ], [
            'judul.required'          => 'Judul dokumen wajib diisi.',
            'tanggal_terbit.required' => 'Tanggal terbit wajib diisi.',
            'kategori_id.required'    => 'Kategori dokumen wajib dipilih.',
            'owner_user_id.required'  => 'Pilih minimal satu pengguna yang dapat mengakses.',
            'file.required'           => 'File dokumen wajib diunggah.',
            'file.max'                => 'Ukuran file maksimal 20MB.',
        ]);

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

            // Upload file ke Minio
            $filePath = $request->file('file')->store('dokumen/dosen', 'minio');

            // BUAT RECORD DOKUMEN
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

            // BERIKAN HAK AKSES READ
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
