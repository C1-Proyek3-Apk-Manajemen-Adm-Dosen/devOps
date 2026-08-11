<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\ProfilDosen;
use App\Services\PddiktiScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PortofolioController extends Controller
{
    protected $scraperService;

    public function __construct(PddiktiScraperService $scraperService)
    {
        $this->scraperService = $scraperService;
    }

    /**
     * Tampilkan halaman portofolio
     */
    public function index()
    {
        $user = Auth::user();
        $profil = $user->profilDosen;

        // Jika profil ada, ambil data biodata untuk ditampilkan
        $biodata = null;
        if ($profil) {
            $biodata = [
                'nama_lengkap' => $profil->nama_lengkap,
                'nidn' => $profil->nidn,
                'jenis_kelamin' => $profil->jenis_kelamin,
                'perguruan_tinggi' => $profil->perguruan_tinggi,
                'program_studi' => $profil->program_studi,
                'jabatan_fungsional' => $profil->jabatan_fungsional,
                'status_aktivitas' => $profil->status_aktivitas,
                'jumlah_penelitian' => $profil->jumlah_penelitian,
                'jumlah_publikasi' => $profil->jumlah_publikasi,
                'jumlah_pengabdian' => $profil->jumlah_pengabdian,
                'riwayat_pendidikan' => $profil->riwayat_pendidikan ?? [],
                'penelitian' => $profil->penelitian ?? [],
                'pengabdian' => $profil->pengabdian ?? [],
                'publikasi' => $profil->publikasi ?? [],
                'hki' => $profil->hki ?? [],
            ];
        }

        return view('dosen.portofolio', compact('profil', 'biodata'));
    }

    /**
     * Form pencarian dosen di PDDikti
     */
    public function searchForm()
    {
        $user = Auth::user();
        $profil = $user->profilDosen;

        return view('dosen.portofolio-search', compact('profil'));
    }

    /**
     * API: Cari dosen di PDDikti
     */
    public function searchPddikti(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|min:3',
        ]);

        try {
            $results = $this->scraperService->searchDosen($request->nama);

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => count($results),
            ]);
        } catch (\Exception $e) {
            Log::error('Search PDDikti error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import biodata dari PDDikti berdasarkan pilihan user
     */
    public function importFromPddikti(Request $request)
    {
        $request->validate([
            'detail_url' => 'required|url',
            'nama' => 'required|string',
        ]);

        try {
            Log::info('Starting PDDikti import for URL: ' . $request->detail_url);

            // Scrape biodata lengkap
            $biodata = $this->scraperService->extractBiodataFromDetailPage($request->detail_url);

            if (!$biodata || empty($biodata['nama_lengkap'])) {
                Log::error('Failed to extract biodata from PDDikti');
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari PDDikti. Halaman mungkin kosong atau struktur berubah.',
                ], 400);
            }

            $user = Auth::user();

            // Simpan atau update profil
            $profil = ProfilDosen::updateOrCreate(
                ['id_user' => $user->id_user],
                [
                    'nidn' => $biodata['nidn'],
                    'nip' => $biodata['nip'],
                    'nama_lengkap' => $biodata['nama_lengkap'],
                    'tempat_lahir' => $biodata['tempat_lahir'],
                    'tanggal_lahir' => $biodata['tanggal_lahir'],
                    'jenis_kelamin' => $biodata['jenis_kelamin'],
                    'jabatan_fungsional' => $biodata['jabatan_fungsional'],
                    'pangkat_golongan' => $biodata['pangkat_golongan'],
                    'status_dosen' => $biodata['status_dosen'],
                    'status_aktivitas' => $biodata['status_aktivitas'] ?? 'Aktif',
                    'perguruan_tinggi' => $biodata['perguruan_tinggi'],
                    'fakultas' => $biodata['fakultas'],
                    'program_studi' => $biodata['program_studi'],
                    'pendidikan_terakhir' => $biodata['pendidikan_terakhir'] ?? null,
                    'riwayat_pendidikan' => $biodata['pendidikan'] ?? [],
                    'penelitian' => $biodata['penelitian'] ?? [],
                    'pengabdian' => $biodata['pengabdian'] ?? [],
                    'publikasi' => $biodata['publikasi'] ?? [],
                    'hki' => $biodata['hki'] ?? [],
                    'jumlah_penelitian' => $biodata['jumlah_penelitian'] ?? 0,
                    'jumlah_publikasi' => $biodata['jumlah_publikasi'] ?? 0,
                    'jumlah_pengabdian' => $biodata['jumlah_pengabdian'] ?? 0,
                    'pddikti_url' => $request->detail_url,
                    'last_scraped_at' => now(),
                    'is_verified' => false,
                ]
            );

            Log::info('Successfully imported PDDikti data for user: ' . $user->id_user);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimpor dari PDDikti! Silakan verifikasi dan lengkapi data tambahan.',
                'data' => $profil->only([
                    'nama_lengkap',
                    'nidn',
                    'perguruan_tinggi',
                    'program_studi',
                    'jumlah_penelitian',
                    'jumlah_publikasi',
                    'jumlah_pengabdian'
                ]),
            ]);
        } catch (\Exception $e) {
            Log::error('Import PDDikti error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update profil manual (data tambahan yang tidak ada di PDDikti)
     */
    public function updateManual(Request $request)
    {
        $request->validate([
            'email_institusi' => 'nullable|email',
            'no_telepon' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'bidang_keahlian' => 'nullable|string|max:500',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();
        $profil = $user->profilDosen;

        if (!$profil) {
            return response()->json([
                'success' => false,
                'message' => 'Profil belum ada. Silakan import dari PDDikti terlebih dahulu.',
            ], 404);
        }

        try {
            // Handle upload foto
            if ($request->hasFile('foto_profil')) {
                // Hapus foto lama
                if ($profil->foto_profil) {
                    Storage::disk('public')->delete($profil->foto_profil);
                }

                $path = $request->file('foto_profil')->store('profil-dosen', 'public');
                $profil->foto_profil = $path;
            }

            // Update field manual
            $profil->update($request->only([
                'email_institusi',
                'no_telepon',
                'bio',
                'bidang_keahlian',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui!',
                'data' => $profil->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Update manual error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal update profil: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verifikasi profil (menandakan data sudah benar)
     */
    public function verifyProfile()
    {
        $user = Auth::user();
        $profil = $user->profilDosen;

        if (!$profil) {
            return response()->json([
                'success' => false,
                'message' => 'Profil tidak ditemukan.',
            ], 404);
        }

        $profil->update(['is_verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diverifikasi!',
        ]);
    }

    /**
     * Refresh data dari PDDikti (re-scrape)
     */
    public function refreshFromPddikti()
    {
        $user = Auth::user();
        $profil = $user->profilDosen;

        if (!$profil || !$profil->pddikti_url) {
            return response()->json([
                'success' => false,
                'message' => 'URL PDDikti tidak ditemukan. Silakan import ulang.',
            ], 404);
        }

        try {
            // Clear cache dulu
            $this->scraperService->clearCache($profil->nama_lengkap);

            // Re-scrape
            $biodata = $this->scraperService->extractBiodataFromDetailPage($profil->pddikti_url);

            if (!$biodata) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal refresh data dari PDDikti.',
                ], 400);
            }

            // Update hanya field dari PDDikti, jangan overwrite data manual
            $profil->update([
                'jabatan_fungsional' => $biodata['jabatan_fungsional'],
                'pangkat_golongan' => $biodata['pangkat_golongan'],
                'status_dosen' => $biodata['status_dosen'],
                'status_aktivitas' => $biodata['status_aktivitas'] ?? $profil->status_aktivitas,
                'riwayat_pendidikan' => $biodata['pendidikan'] ?? $profil->riwayat_pendidikan,
                'penelitian' => $biodata['penelitian'] ?? $profil->penelitian,
                'pengabdian' => $biodata['pengabdian'] ?? $profil->pengabdian,
                'publikasi' => $biodata['publikasi'] ?? $profil->publikasi,
                'hki' => $biodata['hki'] ?? $profil->hki,
                'jumlah_penelitian' => $biodata['jumlah_penelitian'] ?? $profil->jumlah_penelitian,
                'jumlah_publikasi' => $biodata['jumlah_publikasi'] ?? $profil->jumlah_publikasi,
                'jumlah_pengabdian' => $biodata['jumlah_pengabdian'] ?? $profil->jumlah_pengabdian,
                'last_scraped_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui dari PDDikti!',
                'data' => $profil->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Refresh PDDikti error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
