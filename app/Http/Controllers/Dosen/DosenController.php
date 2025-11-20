<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;
use App\Models\AccessControl;
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
        
        $filterKategori = [
            'bukti-pengajaran' => 'Bukti Pengajaran',
            'bkd' => 'BKD',
            'rps' => 'RPS',
            'skp' => 'SKP',
        ];

        $query = Dokumen::with(['kategori', 'creator'])
            ->where('created_by', Auth::id())
            ->orderBy('created_at', 'desc');

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
        
        return view('dosen.detail-dokumen', compact('dokumen'));
    }

    /**
     * Download dokumen
     */
    public function download($id)
    {
        $dokumen = Dokumen::where('created_by', Auth::id())->findOrFail($id);

        if (!$dokumen->file_path) {
            return back()->with('error', 'Path file tidak ditemukan di database.');
        }

        try {
            if (!Storage::disk('minio')->exists($dokumen->file_path)) {
                return back()->with('error', 'File fisik tidak ditemukan di server penyimpanan (MinIO).');
            }

            $extension = pathinfo($dokumen->file_path, PATHINFO_EXTENSION);
            $ext = $extension ? '.' . $extension : ''; 
            $cleanTitle = preg_replace('/[^A-Za-z0-9\- ]/', '', $dokumen->judul);
            $downloadName = $cleanTitle . $ext;

            return Storage::disk('minio')->download($dokumen->file_path, $downloadName);

        } catch (\Exception $e) {
            Log::error("Gagal download file ID {$id}: " . $e->getMessage());
            return back()->with('error', 'Gagal menghubungi server penyimpanan. Detail: ' . $e->getMessage());
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
                $query->where('status', 'CONFIRMED')
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
                    'status' => 'CONFIRMED',
                    'created_by' => Auth::id(),
                ]);

                $message = 'Hak akses berhasil diperbarui!';
            } else {
                AccessControl::create([
                    'document_id' => $id,
                    'grantee_user_id' => $request->user_id,
                    'perm' => $request->permission,
                    'status' => 'CONFIRMED',
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
}