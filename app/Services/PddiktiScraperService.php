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
    protected $cacheTime = 86400; // 24 jam

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false, // Jika ada SSL issue
        ]);
        $this->baseUrl = env('PDDIKTI_BASE_URL', 'https://pddikti.kemdikbud.go.id');
    }

    /**
     * Mencari dosen dan return list hasil (multiple results)
     */
    public function searchDosen(string $namaDosen): array
    {
        $cacheKey = 'pddikti_search_' . md5($namaDosen);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($namaDosen) {
            $urlPencarian = $this->baseUrl . '/search';

            try {
                $response = $this->client->request('GET', $urlPencarian, [
                    'query' => [
                        'q' => $namaDosen,
                        'jenis' => 'dosen'
                    ],
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    ]
                ]);

                $html = $response->getBody()->getContents();
                return $this->parseSearchResults($html);
            } catch (\Exception $e) {
                Log::error('PDDikti Search Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Parse hasil pencarian menjadi array dosen
     */
    protected function parseSearchResults(string $html): array
    {
        $crawler = new Crawler($html);
        $results = [];

        // PENTING: Selector ini harus disesuaikan dengan struktur HTML PDDikti saat ini
        // Berikut beberapa kemungkinan selector:

        // Opsi 1: Jika menggunakan card/box per dosen
        $crawler->filter('.lecturer-item, .dosen-item, .search-result-item')->each(function (Crawler $node) use (&$results) {
            try {
                $nama = $node->filter('h3, .name, .lecturer-name')->first()->text();
                $nidn = $this->extractText($node, '.nidn, .id-number');
                $prodi = $this->extractText($node, '.prodi, .program-studi');
                $perguruan = $this->extractText($node, '.university, .perguruan-tinggi');

                // Ambil link detail
                $linkNode = $node->filter('a[href*="dosen"], a[href*="lecturer"]')->first();
                $detailUrl = $linkNode->count() > 0 ? $linkNode->attr('href') : null;

                if ($nama && $detailUrl) {
                    $results[] = [
                        'nama' => trim($nama),
                        'nidn' => trim($nidn),
                        'program_studi' => trim($prodi),
                        'perguruan_tinggi' => trim($perguruan),
                        'detail_url' => $this->normalizeUrl($detailUrl),
                    ];
                }
            } catch (\Exception $e) {
                // Skip item yang error
            }
        });

        return $results;
    }

    /**
     * Mengambil biodata lengkap dosen by NIDN atau nama
     */
    public function getDosenBiodata(string $identifier, string $type = 'nama'): ?array
    {
        $cacheKey = 'pddikti_biodata_' . md5($identifier);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($identifier, $type) {
            // Jika by nama, cari dulu
            if ($type === 'nama') {
                $searchResults = $this->searchDosen($identifier);
                if (empty($searchResults)) {
                    return null;
                }
                $detailUrl = $searchResults[0]['detail_url'];
            } else {
                // Jika by NIDN, langsung ke detail
                $detailUrl = $this->baseUrl . '/dosen/' . $identifier;
            }

            return $this->extractBiodataFromDetailPage($detailUrl);
        });
    }

    /**
     * Extract biodata dari halaman detail
     */
    protected function extractBiodataFromDetailPage(string $url): ?array
    {
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ]
            ]);

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $biodata = [
                'url_sumber' => $url,
                'scraped_at' => now()->toDateTimeString(),
            ];

            // === DATA UTAMA ===
            $biodata['nama_lengkap'] = $this->extractText($crawler, 'h1, .page-title, .lecturer-name');
            $biodata['nidn'] = $this->extractText($crawler, '.nidn, [class*="nidn"]');
            $biodata['nip'] = $this->extractText($crawler, '.nip, [class*="nip"]');
            $biodata['tempat_lahir'] = $this->extractText($crawler, '[class*="tempat-lahir"]');
            $biodata['tanggal_lahir'] = $this->extractText($crawler, '[class*="tanggal-lahir"]');
            $biodata['jenis_kelamin'] = $this->extractText($crawler, '[class*="jenis-kelamin"]');

            // === JABATAN & STATUS ===
            $biodata['jabatan_fungsional'] = $this->extractText($crawler, '[class*="jabatan"]');
            $biodata['pangkat_golongan'] = $this->extractText($crawler, '[class*="pangkat"], [class*="golongan"]');
            $biodata['status_dosen'] = $this->extractText($crawler, '[class*="status"]');

            // === INSTITUSI ===
            $biodata['perguruan_tinggi'] = $this->extractText($crawler, '[class*="perguruan-tinggi"], [class*="university"]');
            $biodata['program_studi'] = $this->extractText($crawler, '[class*="program-studi"], [class*="prodi"]');
            $biodata['fakultas'] = $this->extractText($crawler, '[class*="fakultas"]');

            // === PENDIDIKAN ===
            $biodata['pendidikan'] = $this->extractEducationHistory($crawler);

            // === PENELITIAN & PUBLIKASI ===
            $biodata['jumlah_penelitian'] = $this->extractCount($crawler, '[class*="penelitian"]');
            $biodata['jumlah_publikasi'] = $this->extractCount($crawler, '[class*="publikasi"]');
            $biodata['jumlah_pengabdian'] = $this->extractCount($crawler, '[class*="pengabdian"]');

            // === SERTIFIKASI ===
            $biodata['sertifikat_pendidik'] = $this->extractText($crawler, '[class*="sertifikat"]');

            // Validasi data minimal
            if (empty($biodata['nama_lengkap']) || empty($biodata['nidn'])) {
                Log::warning('PDDikti: Data tidak lengkap dari ' . $url);
                return null;
            }

            return $biodata;
        } catch (\Exception $e) {
            Log::error('PDDikti Extract Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract riwayat pendidikan
     */
    protected function extractEducationHistory(Crawler $crawler): array
    {
        $education = [];

        try {
            $crawler->filter('[class*="pendidikan"] li, .education-item')->each(function (Crawler $node) use (&$education) {
                $text = trim($node->text());
                if (!empty($text)) {
                    $education[] = $text;
                }
            });
        } catch (\Exception $e) {
            // Return empty jika gagal
        }

        return $education;
    }

    /**
     * Helper: Extract text dari selector
     */
    protected function extractText(Crawler $crawler, string $selector): ?string
    {
        try {
            $node = $crawler->filter($selector)->first();
            return $node->count() > 0 ? trim($node->text()) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper: Extract angka/count
     */
    protected function extractCount(Crawler $crawler, string $selector): int
    {
        $text = $this->extractText($crawler, $selector);
        if (!$text) return 0;

        preg_match('/\d+/', $text, $matches);
        return isset($matches[0]) ? (int)$matches[0] : 0;
    }

    /**
     * Helper: Normalize URL
     */
    protected function normalizeUrl(string $url): string
    {
        if (str_starts_with($url, 'http')) {
            return $url;
        }
        return $this->baseUrl . (str_starts_with($url, '/') ? '' : '/') . $url;
    }

    /**
     * Clear cache untuk dosen tertentu
     */
    public function clearCache(string $identifier): void
    {
        Cache::forget('pddikti_search_' . md5($identifier));
        Cache::forget('pddikti_biodata_' . md5($identifier));
    }
}
