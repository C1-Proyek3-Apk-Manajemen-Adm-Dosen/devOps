<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;
use App\Models\AccessControl;
use App\Models\VersiDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    /**
     * Tampilkan halaman Dokumen Saya
     */
    public function dokumenSaya(Request $request)
    {
        $tab = $request->get('tab', 'semua');
        $search = $request->get('search');
        
        $filterKategori = [
            'bukti-pengajaran' => 'Bukti Pengajaran',
            'bkd' => 'BKD',
            'rps' => 'RPS',
            'skp' => 'SKP',
        ];

        $query = Dokumen::with(['kategori', 'creator', 'versi' => function($q) {
                $q->latest('nomor_versi'); 
            }])
            ->where('created_by', Auth::id())
            ->orderBy('created_at', 'desc');

        $query->whereHas('kategori', function ($q) {
            $q->whereIn('nama_kategori', ['Bukti Pengajaran', 'BKD', 'RPS', 'SKP']);
        });

        if ($search) {
            $query->whereRaw('LOWER(judul) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        if ($tab !== 'semua' && isset($filterKategori[$tab])) {
            $kategoriNama = $filterKategori[$tab];
            $query->whereHas('kategori', function ($q) use ($kategoriNama) {
                $q->where('nama_kategori', $kategoriNama);
            });
        }

        $total = (clone $query)->count();
        $dokumens = $query->paginate(5)->withQueryString();

        $tabs = [
            'semua' => 'Semua',
            'bukti-pengajaran' => 'Bukti Pengajaran',
            'bkd' => 'BKD',
            'rps' => 'RPS',
            'skp' => 'SKP',
        ];

        return view('dosen.dokumen', compact('dokumens', 'tab', 'total', 'tabs'));
    }

    /**
     * Detail dokumen
     */
    public function detailDokumen($id)
    {
        $dokumen = Dokumen::with(['kategori', 'versi'])
            ->where('created_by', Auth::id())
            ->findOrFail($id);
        
        // Cek file exists
        $fileExists = false;
        if ($dokumen->file_path) {
            try {
                $fileExists = Storage::disk('minio')->exists($dokumen->file_path);
            } catch (\Exception $e) {
                Log::warning("File check failed for dokumen ID {$id}: " . $e->getMessage());
                $fileExists = false;
            }
        }
        

        $latestAccess = AccessControl::where('document_id', $id)
            ->whereHas('granteeUser', function($q) {
                $q->where('role', 'koordinator'); 
            })
            ->latest('created_at')
            ->first();
        
        $statusDokumen = $latestAccess ? $latestAccess->status : 'PENDING';

        return view('dosen.detail-dokumen', compact('dokumen', 'fileExists', 'statusDokumen'));
    }

    /**
     * Download dokumen
     */
    public function download($id, Request $request)
    {
        $dokumen = Dokumen::where('created_by', Auth::id())->findOrFail($id);
        
        $versiNomor = $request->get('versi');
        
        if ($versiNomor) {
            $versi = VersiDokumen::where('dokumen_id', $id)
                ->where('nomor_versi', $versiNomor)
                ->firstOrFail();
            
            $filePath = $versi->file_path;
            $version = '_v' . $versiNomor;
        } else {
            // Download versi terakhir
            $versiTerakhir = $dokumen->versi()->latest('nomor_versi')->first();
            if ($versiTerakhir) {
                $filePath = $versiTerakhir->file_path;
                $version = '_v' . $versiTerakhir->nomor_versi;
            } else {
                $filePath = $dokumen->file_path;
                $version = '';
            }
        }

        if (!$filePath) {
            return back()->with('error', 'Path file tidak ditemukan.');
        }

        try {
            if (!Storage::disk('minio')->exists($filePath)) {
                return back()->with('error', 'File tidak ditemukan di server.');
            }

            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $cleanTitle = preg_replace('/[^A-Za-z0-9\- ]/', '', $dokumen->judul);
            $downloadName = $cleanTitle . $version . '.' . $extension;

            return Storage::disk('minio')->download($filePath, $downloadName);

        } catch (\Exception $e) {
            Log::error("Download gagal: " . $e->getMessage());
            return back()->with('error', 'Gagal download file.');
        }
    }

    /**
     * Show edit hak akses page
     */
    public function editHakAkses($id)
    {
        $dokumen = Dokumen::with([
            'kategori',
            'accessControls' => function ($query) {
                $query->where('status', 'ACC')
                      ->orderBy('created_at', 'desc');
            },
            'accessControls.granteeUser'
        ])->where('created_by', Auth::id())->findOrFail($id);

        $users = User::where('id_user', '!=', Auth::id())
            ->where('status', true)
            ->select('id_user', 'nama_lengkap', 'email', 'role')
            ->orderBy('nama_lengkap')
            ->get();

        return view('dosen.edit-hak-akses', compact('dokumen', 'users'));
    }

    /**
     * Update hak akses dokumen
     */
    public function updateHakAkses(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id_user',
            'permission' => 'required|in:READ,EDIT,OWNER,COMMENT',
        ]);

        try {
            DB::beginTransaction();

            $dokumen = Dokumen::where('created_by', Auth::id())->findOrFail($id);

            $existingAccess = AccessControl::where('document_id', $id)
                ->where('grantee_user_id', $request->user_id)
                ->first();

            if ($existingAccess) {
                $existingAccess->update([
                    'perm' => $request->permission,
                    'status' => 'ACC',
                    'created_by' => Auth::id(),
                ]);

                $message = 'Hak akses berhasil diperbarui!';
            } else {
                AccessControl::create([
                    'document_id' => $id,
                    'grantee_user_id' => $request->user_id,
                    'perm' => $request->permission,
                    'status' => 'ACC',
                    'created_at' => now(),
                    'created_by' => Auth::id(),
                ]);

                $message = 'Hak akses berhasil ditambahkan!';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan hak akses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove hak akses
     */
    public function removeHakAkses(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id_user',
        ]);

        try {
            $deleted = AccessControl::where('document_id', $id)
                ->where('grantee_user_id', $request->user_id)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hak akses berhasil dihapus!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Hak akses tidak ditemukan'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus hak akses: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function uploadVersi(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB
            'catatan_perubahan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Cek dokumen milik user
            $dokumen = Dokumen::where('created_by', Auth::id())->findOrFail($id);

            // Dapatkan versi terakhir
            $versiTerakhir = $dokumen->versi()->latest('nomor_versi')->first();
            $nomorVersiBaru = $versiTerakhir ? $versiTerakhir->nomor_versi + 1 : 1;

            // Upload file baru ke MinIO
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = 'dokumen_' . $id . '_v' . $nomorVersiBaru . '_' . time() . '.' . $extension;
            $filePath = $file->storeAs('dokumen', $filename, 'minio');

            // Simpan ke versi_dokumen dengan catatan
            VersiDokumen::create([
                'dokumen_id' => $id,
                'nomor_versi' => $nomorVersiBaru,
                'file_path' => $filePath,
                'catatan_perubahan' => $request->catatan_perubahan, 
                'tanggal_dokumen' => now(),
                'upload_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('dosen.detail-dokumen', $id)
                ->with('success', 'Versi baru (v' . $nomorVersiBaru . ') berhasil diupload!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Upload versi gagal: " . $e->getMessage());
            
            return back()->with('error', 'Gagal upload versi baru: ' . $e->getMessage());
        }
    }
}