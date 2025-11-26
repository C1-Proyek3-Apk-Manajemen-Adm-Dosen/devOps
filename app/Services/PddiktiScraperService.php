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
    protected $cacheTime = 3600;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                'Referer' => 'https://pddikti.kemdikbud.go.id/',
            ]
        ]);
        $this->baseUrl = 'https://pddikti.kemdikbud.go.id';
    }

    /**
     * Mencari dosen - METODE BARU dengan multiple fallback
     */
    public function searchDosen(string $namaDosen): array
    {
        // Hapus cache untuk testing
        // Cache::forget('pddikti_search_' . md5($namaDosen));
        
        $cacheKey = 'pddikti_search_' . md5($namaDosen);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($namaDosen) {
            try {
                // METODE 1: Cari via halaman search
                $results = $this->searchViaWebPage($namaDosen);
                
                if (!empty($results)) {
                    Log::info('PDDikti: Found via web search', ['count' => count($results)]);
                    return $results;
                }

                // METODE 2: Cari via API (jika ada)
                $results = $this->searchViaAPI($namaDosen);
                
                if (!empty($results)) {
                    Log::info('PDDikti: Found via API', ['count' => count($results)]);
                    return $results;
                }

                Log::warning('PDDikti: No results found for: ' . $namaDosen);
                return [];

            } catch (\Exception $e) {
                Log::error('PDDikti Search Error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return [];
            }
        });
    }

    /**
     * Search via halaman web
     */
    protected function searchViaWebPage(string $namaDosen): array
    {
        try {
            $url = $this->baseUrl . '/search';
            
            $response = $this->client->request('GET', $url, [
                'query' => [
                    'q' => $namaDosen,
                    'jenis' => 'dosen'
                ]
            ]);

            $html = $response->getBody()->getContents();
            
            // Log HTML untuk debugging
            Log::debug('PDDikti HTML Length: ' . strlen($html));
            
            return $this->parseSearchResults($html);

        } catch (\Exception $e) {
            Log::error('Search via web error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search via API endpoint (jika tersedia)
     */
    protected function searchViaAPI(string $namaDosen): array
    {
        try {
            // Beberapa endpoint API yang mungkin:
            $endpoints = [
                '/api/search/dosen',
                '/api/v1/dosen/search',
                '/search/dosen/json',
            ];

            foreach ($endpoints as $endpoint) {
                try {
                    $response = $this->client->request('GET', $this->baseUrl . $endpoint, [
                        'query' => ['q' => $namaDosen]
                    ]);

                    $data = json_decode($response->getBody()->getContents(), true);
                    
                    if (isset($data['data']) && is_array($data['data'])) {
                        return $this->normalizeAPIResults($data['data']);
                    }
                } catch (\Exception $e) {
                    // Try next endpoint
                    continue;
                }
            }

        } catch (\Exception $e) {
            Log::debug('API search not available: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Parse hasil pencarian HTML dengan multiple selector
     */
    protected function parseSearchResults(string $html): array
    {
        $crawler = new Crawler($html);
        $results = [];

        // STRATEGI 1: Cari semua link yang mengandung "detail-dosen"
        try {
            $crawler->filter('a[href*="/detail-dosen/"]')->each(function (Crawler $node) use (&$results) {
                try {
                    $url = $node->attr('href');
                    $text = trim($node->text());
                    
                    if (!empty($text) && strlen($text) < 200) {
                        $results[] = [
                            'nama' => $text,
                            'detail_url' => $this->normalizeUrl($url),
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip
                }
            });
        } catch (\Exception $e) {
            Log::debug('Strategy 1 failed: ' . $e->getMessage());
        }

        // STRATEGI 2: Cari berdasarkan class atau struktur umum
        if (empty($results)) {
            try {
                $crawler->filter('[class*="result"], [class*="item"], .card')->each(function (Crawler $node) use (&$results) {
                    try {
                        $linkNode = $node->filter('a')->first();
                        if ($linkNode->count() > 0) {
                            $url = $linkNode->attr('href');
                            if (strpos($url, 'detail-dosen') !== false) {
                                $results[] = [
                                    'nama' => trim($node->text()),
                                    'detail_url' => $this->normalizeUrl($url),
                                ];
                            }
                        }
                    } catch (\Exception $e) {
                        // Skip
                    }
                });
            } catch (\Exception $e) {
                Log::debug('Strategy 2 failed: ' . $e->getMessage());
            }
        }

        // STRATEGI 3: Parse manual dari text content
        if (empty($results)) {
            try {
                $bodyText = $crawler->filter('body')->text();
                
                // Cek apakah ada teks "tidak ditemukan" atau "no result"
                if (stripos($bodyText, 'tidak ditemukan') !== false || 
                    stripos($bodyText, 'no result') !== false ||
                    stripos($bodyText, 'not found') !== false) {
                    Log::info('PDDikti explicitly says: no results');
                    return [];
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // Hapus duplikat
        $results = collect($results)
            ->unique('detail_url')
            ->filter(fn($item) => !empty($item['nama']))
            ->values()
            ->all();

        Log::info('PDDikti search results', [
            'query' => 'search',
            'found' => count($results)
        ]);

        return $results;
    }

    /**
     * Normalize hasil API
     */
    protected function normalizeAPIResults(array $data): array
    {
        return collect($data)->map(function($item) {
            return [
                'nama' => $item['nama'] ?? $item['name'] ?? '',
                'nidn' => $item['nidn'] ?? '',
                'program_studi' => $item['prodi'] ?? $item['program_studi'] ?? '',
                'perguruan_tinggi' => $item['pt'] ?? $item['perguruan_tinggi'] ?? '',
                'detail_url' => $item['url'] ?? $item['detail_url'] ?? '',
            ];
        })->filter(fn($item) => !empty($item['detail_url']))->all();
    }

    /**
     * Extract biodata dari halaman detail
     */
    public function extractBiodataFromDetailPage(string $url): ?array
    {
        try {
            $response = $this->client->request('GET', $url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $biodata = [
                'url_sumber' => $url,
                'scraped_at' => now()->toDateTimeString(),
            ];

            // Extract dengan multiple strategies
            $biodata['nama_lengkap'] = $this->extractFieldValue($crawler, 'Nama');
            $biodata['jenis_kelamin'] = $this->extractFieldValue($crawler, 'Jenis Kelamin');
            $biodata['perguruan_tinggi'] = $this->extractFieldValue($crawler, 'Perguruan Tinggi');
            $biodata['program_studi'] = $this->extractFieldValue($crawler, 'Program Studi');
            $biodata['jabatan_fungsional'] = $this->extractFieldValue($crawler, 'Jabatan Fungsional');
            $biodata['pendidikan_terakhir'] = $this->extractFieldValue($crawler, 'Pendidikan Terakhir');
            $biodata['status_ikatan_kerja'] = $this->extractFieldValue($crawler, 'Status Ikatan Kerja');
            $biodata['status_aktivitas'] = $this->extractFieldValue($crawler, 'Status Aktivitas');

            // Riwayat pendidikan & portofolio
            $biodata['riwayat_pendidikan'] = $this->extractEducationHistory($crawler);
            $biodata['penelitian'] = $this->extractTableData($crawler, 'Penelitian');
            $biodata['publikasi'] = $this->extractTableData($crawler, 'Publikasi');
            $biodata['pengabdian'] = $this->extractTableData($crawler, 'Pengabdian');

            $biodata['jumlah_penelitian'] = count($biodata['penelitian']);
            $biodata['jumlah_publikasi'] = count($biodata['publikasi']);
            $biodata['jumlah_pengabdian'] = count($biodata['pengabdian']);

            if (empty($biodata['nama_lengkap'])) {
                Log::warning('PDDikti: Empty name from ' . $url);
                return null;
            }

            return $biodata;

        } catch (\Exception $e) {
            Log::error('Extract biodata error: ' . $e->getMessage());
            return null;
        }
    }

    // ... methods lainnya tetap sama ...

    protected function extractFieldValue(Crawler $crawler, string $label): ?string
    {
        try {
            $allText = $crawler->filter('body')->text();
            $lines = explode("\n", $allText);
            
            foreach ($lines as $i => $line) {
                if (stripos($line, $label) !== false) {
                    $parts = preg_split('/\s{2,}/', trim($line), 2);
                    if (count($parts) > 1) {
                        return trim($parts[1]);
                    }
                    
                    if (isset($lines[$i + 1])) {
                        $nextLine = trim($lines[$i + 1]);
                        if (!empty($nextLine)) {
                            return $nextLine;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore
        }
        return null;
    }

    protected function extractEducationHistory(Crawler $crawler): array
    {
        $education = [];
        try {
            $crawler->filter('table')->each(function (Crawler $table) use (&$education) {
                $headerText = $table->filter('thead')->text();
                if (stripos($headerText, 'Perguruan Tinggi') !== false) {
                    $table->filter('tbody tr')->each(function (Crawler $row) use (&$education) {
                        $cols = $row->filter('td');
                        if ($cols->count() >= 3) {
                            $education[] = [
                                'perguruan_tinggi' => trim($cols->eq(0)->text()),
                                'gelar' => trim($cols->eq(1)->text()),
                                'tahun' => trim($cols->eq(2)->text()),
                                'jenjang' => $cols->count() >= 4 ? trim($cols->eq(3)->text()) : '',
                            ];
                        }
                    });
                }
            });
        } catch (\Exception $e) {
            // Ignore
        }
        return $education;
    }

    protected function extractTableData(Crawler $crawler, string $keyword): array
    {
        $data = [];
        try {
            $crawler->filter('table')->each(function (Crawler $table) use (&$data, $keyword) {
                if (stripos($table->text(), $keyword) !== false) {
                    $table->filter('tbody tr')->each(function (Crawler $row) use (&$data) {
                        $cols = $row->filter('td');
                        if ($cols->count() >= 2) {
                            $data[] = [
                                'judul' => trim($cols->eq(1)->text()),
                                'tahun' => $cols->count() >= 3 ? trim($cols->eq(2)->text()) : '',
                            ];
                        }
                    });
                }
            });
        } catch (\Exception $e) {
            // Ignore
        }
        return $data;
    }

    protected function normalizeUrl(string $url): string
    {
        if (str_starts_with($url, 'http')) {
            return $url;
        }
        return $this->baseUrl . (str_starts_with($url, '/') ? '' : '/') . $url;
    }

    public function clearCache(string $identifier): void
    {
        Cache::forget('pddikti_search_' . md5($identifier));
        Cache::forget('pddikti_biodata_' . md5($identifier));
    }
}