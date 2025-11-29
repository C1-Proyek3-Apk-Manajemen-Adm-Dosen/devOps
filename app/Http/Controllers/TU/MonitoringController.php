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

        $query = Dokumen::with(['kategori', 'creator', 'versi' => function($q) {
                $q->latest('nomor_versi'); 
            }])
            ->forTU();

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

    /**
     * API: Get semua data untuk modal (users + existing access)
     * Endpoint ini mengembalikan:
     * - allUsers: semua pengguna
     * - existingAccess: pengguna yang sudah punya akses untuk dokumen ini
     */
    public function getModalData($dokumenId)
    {
        try {
            // Verifikasi dokumen
            $dokumen = Dokumen::findOrFail($dokumenId);

            $allUsers = User::where('id_user', '!=', auth()->id())
                ->where('status', true)
                ->select('id_user', 'nama_lengkap', 'email', 'role')
                ->orderBy('nama_lengkap')
                ->get()
                ->map(function($user) {
                    return [
                        'id_user' => $user->id_user,
                        'nama_lengkap' => $user->nama_lengkap,
                        'email' => $user->email,
                        'role' => $user->role,
                    ];
                });

            // Ambil existing access dengan status ACC, PENDING, TOLAK, REVISI
            $existingAccess = AccessControl::where('document_id', $dokumenId)
                ->whereIn('status', ['PENDING', 'ACC', 'TOLAK', 'REVISI'])
                ->with(['granteeUser' => function($q) {
                    $q->select('id_user', 'nama_lengkap', 'email', 'role');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($access) {
                    return [
                        'id' => $access->id,
                        'grantee_user_id' => $access->grantee_user_id,
                        'document_id' => $access->document_id,
                        'perm' => $access->perm,
                        'status' => $access->status,
                        'grantee_user' => [
                            'id_user' => $access->granteeUser->id_user ?? null,
                            'nama_lengkap' => $access->granteeUser->nama_lengkap ?? null,
                            'email' => $access->granteeUser->email ?? null,
                            'role' => $access->granteeUser->role ?? null,
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'allUsers' => $allUsers,
                'existingAccess' => $existingAccess,
            ]);

        } catch (\Exception $e) {
            Log::error("Get modal data error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function detailPage($id)
    {
        $dokumen = Dokumen::with(['kategori', 'versi', 'creator'])->findOrFail($id);
        
        $allFilePaths = collect([$dokumen->file_path])
            ->merge($dokumen->versi->pluck('file_path'))
            ->filter()
            ->unique()
            ->values();
        
        $fileExistsMap = [];
        foreach ($allFilePaths as $path) {
            try {
                $fileExistsMap[$path] = Storage::disk('minio')->exists($path);
            } catch (\Exception $e) {
                Log::warning("File check failed for {$path}: " . $e->getMessage());
                $fileExistsMap[$path] = false;
            }
        }
        
        $fileExists = $fileExistsMap[$dokumen->file_path] ?? false;

        $latestAccess = AccessControl::where('document_id', $id)
            ->whereHas('granteeUser', function($q) {
                $q->where('role', 'koordinator'); 
            })
            ->latest('created_at')
            ->first();
        
        $statusDokumen = $latestAccess ? $latestAccess->status : 'PENDING';

        return view('tu.detail-dokumen', compact('dokumen', 'fileExists', 'fileExistsMap', 'statusDokumen'));
    }

    public function download($id, Request $request)
    {
        $dokumen = Dokumen::with(['versi'])->findOrFail($id);
        
        $versiNomor = $request->get('versi');
        
        if ($versiNomor) {
            // Download versi spesifik
            $versi = \App\Models\VersiDokumen::where('dokumen_id', $id)
                ->where('nomor_versi', $versiNomor)
                ->firstOrFail();
            
            $filePath = $versi->file_path;
            $version = '_v' . $versiNomor;
        } else {
            // Download versi terakhir
            $latestVersion = $dokumen->versi
                ->sortByDesc('nomor_versi')
                ->firstWhere(fn($v) => $v->file_path && Storage::disk('minio')->exists($v->file_path));

            if (!$latestVersion) {
                return back()->with('error', 'File dokumen tidak tersedia atau rusak di server.');
            }
            
            $filePath = $latestVersion->file_path;
            $version = '_v' . $latestVersion->nomor_versi;
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $ext = $extension ? '.' . $extension : '';
        $cleanTitle = preg_replace('/[^A-Za-z0-9\- ]/', '', $dokumen->judul);
        $filename = "{$cleanTitle}{$version}" . $ext;

        return Storage::disk('minio')->download($filePath, $filename);
    }

    /**
     * Upload versi baru dokumen (untuk TU)
     */
    public function uploadVersi(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB
            'catatan_perubahan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Cek dokumen 
            $dokumen = Dokumen::findOrFail($id);

            // Dapatkan versi terakhir
            $versiTerakhir = $dokumen->versi()->latest('nomor_versi')->first();
            $nomorVersiBaru = $versiTerakhir ? $versiTerakhir->nomor_versi + 1 : 1;

            // Upload file baru ke MinIO
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = 'dokumen_' . $id . '_v' . $nomorVersiBaru . '_' . time() . '.' . $extension;
            $filePath = $file->storeAs('dokumen', $filename, 'minio');

            // Simpan ke versi_dokumen dengan catatan
            \App\Models\VersiDokumen::create([
                'dokumen_id' => $id,
                'nomor_versi' => $nomorVersiBaru,
                'file_path' => $filePath,
                'catatan_perubahan' => $request->catatan_perubahan, 
                'tanggal_dokumen' => now(),
                'upload_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('tu.detail-dokumen', $id)
                ->with('success', 'Versi baru (v' . $nomorVersiBaru . ') berhasil diupload!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Upload versi gagal (TU): " . $e->getMessage());
            
            return back()->with('error', 'Gagal upload versi baru: ' . $e->getMessage());
        }
    }
}
