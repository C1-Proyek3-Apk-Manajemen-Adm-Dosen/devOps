<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PddiktiScraperService
{
    protected $client;
    protected $baseUrl;
    protected $apiSearchUrl;
    protected $cacheTime = 3600;

    public function __construct()
    {
        $timeout = config('pddikti.timeout', 30);
        $userAgent = config('pddikti.user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

        $this->client = new Client([
            'timeout' => $timeout,
            'verify' => false,
            'headers' => [
                'User-Agent' => $userAgent,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
            ]
        ]);

        $this->baseUrl = config('pddikti.base_url', 'https://pddikti.kemdiktisaintek.go.id');
        $this->apiSearchUrl = config('pddikti.api_search_url', 'https://api-pddikti.kemdiktisaintek.go.id/pencarian/enc/all/');
        $this->cacheTime = config('pddikti.cache_ttl', 3600);
    }

    /**
     * Mencari dosen via API JSON (dari standalone test)
     */
    public function searchDosen(string $namaDosen): array
    {
        $cacheKey = 'pddikti_search_v3_' . md5($namaDosen);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($namaDosen) {
            try {
                $query = rawurlencode($namaDosen);
                $url = $this->apiSearchUrl . $query;

                $response = $this->client->get($url, [
                    'headers' => [
                        'Accept' => 'application/json, text/plain, */*',
                        'Origin' => $this->baseUrl
                    ]
                ]);

                $body = $response->getBody()->getContents();
                $json = json_decode($body, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('PDDikti JSON decode error: ' . json_last_error_msg());
                    return [];
                }

                $items = $json['data'] ?? $json;
                if (!is_array($items)) {
                    return [];
                }

                $results = [];
                foreach ($items as $item) {
                    if (!is_array($item)) continue;

                    $nama = $item['nama'] ?? $item['name'] ?? $item['nama_lengkap'] ?? null;
                    $pt = $item['perguruan_tinggi'] ?? $item['pt'] ?? $item['nama_pt'] ?? '-';
                    $prodi = $item['program_studi'] ?? $item['prodi'] ?? '-';

                    $detailUrl = null;
                    if (!empty($item['link'])) {
                        $detailUrl = $item['link'];
                    } elseif (!empty($item['enc']) || !empty($item['id'])) {
                        $enc = $item['enc'] ?? $item['id'];
                        $detailUrl = $this->baseUrl . '/detail-dosen/' . $enc;
                    }

                    if ($nama && $detailUrl) {
                        // Fix double domain issue
                        if (strpos($detailUrl, 'http') !== 0) {
                            $detailUrl = $this->baseUrl . $detailUrl;
                        }

                        $results[] = [
                            'nama' => trim($nama),
                            'perguruan_tinggi' => $pt,
                            'program_studi' => $prodi,
                            'detail_url' => $detailUrl,
                            'nidn' => $item['nidn'] ?? $item['NIDN'] ?? ''
                        ];
                    }
                }

                return $results;
            } catch (\Exception $e) {
                Log::error('PDDikti Search Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Extract biodata lengkap dari halaman detail
     * Disesuaikan dengan struktur HTML PDDikti terbaru
     */
    public function extractBiodataFromDetailPage(string $url): ?array
    {
        try {
            $response = $this->client->get($url);
            if ($response->getStatusCode() !== 200) {
                Log::error('Failed to fetch PDDikti page: ' . $response->getStatusCode());
                return null;
            }

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $biodata = [
                'url_sumber' => $url,
                'scraped_at' => now()->toDateTimeString(),
            ];

            // ========== BIODATA UTAMA (dari div.mb-4 dengan <p> label-value) ==========
            $crawler->filter('div.mb-4')->each(function ($node) use (&$biodata) {
                try {
                    $ps = $node->filter('p');
                    if ($ps->count() < 2) return;

                    $label = trim($ps->eq(0)->text());
                    $value = trim($ps->eq(1)->text());

                    $labelLower = strtolower($label);

                    switch ($labelLower) {
                        case 'nama':
                            $biodata['nama_lengkap'] = $value;
                            break;
                        case 'nidn':
                            $biodata['nidn'] = $value;
                            break;
                        case 'nip':
                            $biodata['nip'] = $value;
                            break;
                        case 'jenis kelamin':
                            $biodata['jenis_kelamin'] = $value;
                            break;
                        case 'tempat lahir':
                            $biodata['tempat_lahir'] = $value;
                            break;
                        case 'tanggal lahir':
                            $biodata['tanggal_lahir'] = $value;
                            break;
                        case 'perguruan tinggi':
                            $biodata['perguruan_tinggi'] = $value;
                            break;
                        case 'fakultas':
                            $biodata['fakultas'] = $value;
                            break;
                        case 'program studi':
                            $biodata['program_studi'] = $value;
                            break;
                        case 'jabatan fungsional':
                        case 'jabatan':
                            $biodata['jabatan_fungsional'] = $value;
                            break;
                        case 'pangkat golongan':
                        case 'pangkat/golongan':
                        case 'pangkat':
                            $biodata['pangkat_golongan'] = $value;
                            break;
                        case 'status ikatan kerja':
                        case 'status':
                            $biodata['status_dosen'] = $value;
                            break;
                        case 'pendidikan terakhir':
                            $biodata['pendidikan_terakhir'] = $value;
                            break;
                        case 'status aktivitas':
                            $biodata['status_aktivitas'] = $value;
                            break;
                    }
                } catch (\Exception $e) {
                    // Skip invalid items
                }
            });

            // Set default values for missing fields
            $biodata['nama_lengkap'] = $biodata['nama_lengkap'] ?? null;
            $biodata['nidn'] = $biodata['nidn'] ?? '-';
            $biodata['nip'] = $biodata['nip'] ?? '-';
            $biodata['jenis_kelamin'] = $biodata['jenis_kelamin'] ?? '-';
            $biodata['tempat_lahir'] = $biodata['tempat_lahir'] ?? '-';
            $biodata['tanggal_lahir'] = $biodata['tanggal_lahir'] ?? null;
            $biodata['perguruan_tinggi'] = $biodata['perguruan_tinggi'] ?? null;
            $biodata['fakultas'] = $biodata['fakultas'] ?? '-';
            $biodata['program_studi'] = $biodata['program_studi'] ?? null;
            $biodata['jabatan_fungsional'] = $biodata['jabatan_fungsional'] ?? null;
            $biodata['pangkat_golongan'] = $biodata['pangkat_golongan'] ?? null;
            $biodata['status_dosen'] = $biodata['status_dosen'] ?? null;
            $biodata['sertifikat_pendidik'] = false;

            // ========== RIWAYAT PENDIDIKAN ==========
            $biodata['pendidikan'] = [];
            try {
                $crawler->filter('div[data-value="pendidikan"] table tbody tr')->each(function ($tr) use (&$biodata) {
                    try {
                        $tds = $tr->filter('td');
                        if ($tds->count() >= 4) {
                            $biodata['pendidikan'][] = [
                                'perguruan_tinggi' => trim($tds->eq(0)->text()),
                                'gelar' => trim($tds->eq(1)->text()),
                                'tahun' => trim($tds->eq(2)->text()),
                                'jenjang' => trim($tds->eq(3)->text()),
                            ];
                        }
                    } catch (\Exception $e) {
                        // Skip
                    }
                });
            } catch (\Exception $e) {
                Log::warning('Failed to extract education: ' . $e->getMessage());
            }

            // ========== PORTOFOLIO (Penelitian, Pengabdian, Publikasi, HKI) ==========
            $biodata['penelitian'] = $this->extractPortofolioTable($crawler, 'penelitian');
            $biodata['pengabdian'] = $this->extractPortofolioTable($crawler, 'pengabdian_masyarakat');
            $biodata['publikasi'] = $this->extractPortofolioTable($crawler, 'publikasi_karya');
            $biodata['hki'] = $this->extractPortofolioTable($crawler, 'hki');

            // Hitung jumlah
            $biodata['jumlah_penelitian'] = count($biodata['penelitian']);
            $biodata['jumlah_pengabdian'] = count($biodata['pengabdian']);
            $biodata['jumlah_publikasi'] = count($biodata['publikasi']);

            // Fallback: jika nama kosong, coba ambil dari title
            if (empty($biodata['nama_lengkap'])) {
                try {
                    $title = $crawler->filter('title')->text();
                    $biodata['nama_lengkap'] = trim(str_replace(['PDDikti', '-', 'Biodata Dosen'], '', $title));
                } catch (\Exception $e) {
                    // Ignore
                }
            }

            return $biodata;
        } catch (\Exception $e) {
            Log::error('Extract biodata error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper: Extract data dari tab portofolio berdasarkan data-value
     */
    protected function extractPortofolioTable(Crawler $crawler, string $tabValue): array
    {
        $data = [];
        try {
            $selector = sprintf('#tabs-portofolio div[role="tabpanel"][data-value="%s"] table tbody tr', $tabValue);

            $crawler->filter($selector)->each(function ($tr) use (&$data) {
                try {
                    $tds = $tr->filter('td');
                    if ($tds->count() < 2) return;

                    $item = [];

                    // Biasanya kolom: No | Judul | Jenis | Tahun
                    // Ambil kolom kedua sebagai judul
                    if ($tds->count() >= 2) {
                        $item['judul'] = trim($tds->eq(1)->text());
                    }

                    // Cek kolom terakhir untuk tahun
                    $lastCol = trim($tds->eq($tds->count() - 1)->text());
                    if (preg_match('/\d{4}/', $lastCol)) {
                        $item['tahun'] = $lastCol;
                    } else {
                        $item['tahun'] = '-';
                    }

                    // Jika ada kolom ketiga, itu biasanya jenis karya
                    if ($tds->count() >= 3) {
                        $item['jenis'] = trim($tds->eq(2)->text());
                    }

                    if (!empty($item['judul'])) {
                        $data[] = $item;
                    }
                } catch (\Exception $e) {
                    // Skip
                }
            });
        } catch (\Exception $e) {
            Log::warning("Failed to extract portfolio tab '{$tabValue}': " . $e->getMessage());
        }

        return $data;
    }

    /**
     * Clear cache untuk re-scraping
     */
    public function clearCache(string $identifier): void
    {
        Cache::forget('pddikti_search_v3_' . md5($identifier));
    }
}
