<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\User;
use App\Models\AccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Log; 

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'semua');
        $search = $request->get('search');

        $query = Dokumen::with(['kategori', 'creator'])->forTU();

        if ($search) {
            $query->whereRaw('LOWER(judul) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        if ($tab !== 'semua') {
            $map = [
                'surat-tugas'        => 'Surat Tugas',
                'surat-keputusan'    => 'Surat Keputusan',
                'riwayat-pengajaran' => 'Riwayat Pengajaran',
            ];

            if (isset($map[$tab])) {
                $query->whereHas('kategori', fn($q) => $q->where('nama_kategori', $map[$tab]));
            }
        }

        $dokumens = $query->orderByDesc('created_at')
                        ->paginate(5)
                        ->withQueryString();

        $tabs = [
            'semua'              => 'Semua',
            'surat-tugas'        => 'Surat Tugas',
            'surat-keputusan'    => 'Surat Keputusan',
            'riwayat-pengajaran' => 'Riwayat Pengajaran',
        ];

        $total = $dokumens->total();

        return view('tu.monitoring', compact('dokumens', 'tab', 'tabs', 'total'));
    }

    public function detail($id)
    {
        $dokumen = Dokumen::with(['kategori', 'versi'])->findOrFail($id);

        $kategoriNama = $dokumen->kategori?->nama_kategori ?? 'Tidak Ada Kategori';

        $badgeClass = match($kategoriNama) {
            'Surat Keputusan' => 'bg-purple-100 text-purple-700 border border-purple-200',
            'Surat Tugas' => 'bg-blue-100 text-blue-700 border border-blue-200',
            'Riwayat Pengajaran' => 'bg-green-100 text-green-700 border border-green-200',
            'RPS', 'Rencana Pembelajaran Semester' => 'bg-indigo-100 text-indigo-700 border border-indigo-200',
            'BKD', 'Buku Kerja Dosen' => 'bg-orange-100 text-orange-700 border border-orange-200',
            'SKP' => 'bg-pink-100 text-pink-700 border border-pink-200',
            default => 'bg-gray-100 text-gray-700 border border-gray-200'
        };

        $versiTerbaru = $dokumen->versi()->latest('nomor_versi')->first();

        return response()->json([
            'dokumen_id' => $dokumen->dokumen_id,
            'judul' => $dokumen->judul,
            'nomor_dokumen' => $dokumen->nomor_dokumen,
            'tanggal_terbit' => $dokumen->tanggal_terbit,
            'tanggal_terbit_formatted' => \Carbon\Carbon::parse($dokumen->tanggal_terbit)->translatedFormat('d F Y'),
            'kategori' => $kategoriNama,
            'badge_class' => $badgeClass,
            'deskripsi' => $dokumen->deskripsi ?? 'Tidak ada deskripsi',
            'versi' => $versiTerbaru?->nomor_versi ?? 1,
            'file_path' => $dokumen->file_path,
        ]);
    }

    public function editHakAkses($id)
    {
        $dokumen = Dokumen::with([
            'kategori',
            'accessControls' => function ($query) {
                $query->where('status', 'ACC')
                      ->orderBy('created_at', 'desc');
            },
            'accessControls.granteeUser'
        ])->findOrFail($id);

        $users = User::where('id_user', '!=', auth()->id())
            ->where('status', true)
            ->select('id_user', 'nama_lengkap', 'email', 'role')
            ->orderBy('nama_lengkap')
            ->get();

        return view('tu.edit-hak-akses', compact('dokumen', 'users'));
    }

    public function updateHakAkses(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id_user',
            'permission' => 'required|in:READ,EDIT,OWNER,COMMENT',
        ]);

        try {
            DB::beginTransaction();

            $dokumen = Dokumen::findOrFail($id);

            $existingAccess = AccessControl::where('document_id', $id)
                ->where('grantee_user_id', $request->user_id)
                ->first();

            if ($existingAccess) {
                $existingAccess->update([
                    'perm' => $request->permission,
                    'status' => 'ACC',
                    'created_by' => auth()->id(),
                ]);

                $message = 'Hak akses berhasil diperbarui!';
            } else {
                AccessControl::create([
                    'document_id' => $id,
                    'grantee_user_id' => $request->user_id,
                    'perm' => $request->permission,
                    'status' => 'ACC',
                    'created_at' => now(),
                    'created_by' => auth()->id(),
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

    public function detailPage($id)
    {
        $dokumen = Dokumen::with(['kategori', 'versi'])->findOrFail($id);
        $fileExists = false;
        if ($dokumen->file_path) {
            try {
                $fileExists = Storage::disk('minio')->exists($dokumen->file_path);
            } catch (\Exception $e) {
                Log::warning("File check failed for dokumen ID {$id}: " . $e->getMessage());
                $fileExists = false;
            }
        }

        return view('tu.detail-dokumen', compact('dokumen', 'fileExists'));
    }

    public function download($id)
    {

        $dokumen = Dokumen::findOrFail($id);

        if (!$dokumen->file_path) {
            return back()->with('error', 'Path file tidak ditemukan di database.');
        }

        try {
            // Cek keberadaan file
            if (!Storage::disk('minio')->exists($dokumen->file_path)) {
                return back()->with('error', 'File fisik tidak ditemukan di server penyimpanan (MinIO).');
            }

            $extension = pathinfo($dokumen->file_path, PATHINFO_EXTENSION);
            $ext = $extension ? '.' . $extension : ''; 
            
            $cleanTitle = preg_replace('/[^A-Za-z0-9\- ]/', '', $dokumen->judul);
            $downloadName = $cleanTitle . $ext;

            return Storage::disk('minio')->download($dokumen->file_path, $downloadName);

        } catch (\Exception $e) {
            // Log error aslinya biar bisa dicek di storage/logs/laravel.log
            Log::error("Gagal download file ID {$id}: " . $e->getMessage());

            return back()->with('error', 'Gagal menghubungi server penyimpanan. Pastikan MinIO aktif. Detail: ' . $e->getMessage());
        }
    }
}
