<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\ProfilDosen;
use App\Services\PddiktiScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        return view('dosen.portofolio', compact('profil'));
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

        $results = $this->scraperService->searchDosen($request->nama);

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => count($results),
        ]);
    }

    /**
     * Import biodata dari PDDikti berdasarkan pilihan user
     */
    public function importFromPddikti(Request $request)
    {
        $request->validate([
            'nidn' => 'nullable|string',
            'nama' => 'nullable|string',
            'detail_url' => 'required|url',
        ]);

        try {
            // Scrape biodata lengkap
            $biodata = $this->scraperService->extractBiodataFromDetailPage($request->detail_url);

            if (!$biodata) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari PDDikti. Silakan coba lagi.',
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
                    'perguruan_tinggi' => $biodata['perguruan_tinggi'],
                    'fakultas' => $biodata['fakultas'],
                    'program_studi' => $biodata['program_studi'],
                    'riwayat_pendidikan' => $biodata['pendidikan'],
                    'jumlah_penelitian' => $biodata['jumlah_penelitian'] ?? 0,
                    'jumlah_publikasi' => $biodata['jumlah_publikasi'] ?? 0,
                    'jumlah_pengabdian' => $biodata['jumlah_pengabdian'] ?? 0,
                    'sertifikat_pendidik' => $biodata['sertifikat_pendidik'],
                    'pddikti_url' => $request->detail_url,
                    'last_scraped_at' => now(),
                    'is_verified' => false, // User harus verifikasi manual
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimpor dari PDDikti!',
                'data' => $profil,
            ]);
        } catch (\Exception $e) {
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
            'foto_profil' => 'nullable|image|max:2048', // 2MB
        ]);

        $user = Auth::user();
        $profil = $user->profilDosen;

        if (!$profil) {
            return response()->json([
                'success' => false,
                'message' => 'Profil belum ada. Silakan import dari PDDikti terlebih dahulu.',
            ], 404);
        }

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
            'data' => $profil,
        ]);
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
                'message' => 'URL PDDikti tidak ditemukan.',
            ], 404);
        }

        try {
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
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
