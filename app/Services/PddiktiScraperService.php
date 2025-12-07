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
        // Setup Client
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false, // Bypass SSL issues
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'application/json, text/plain, */*',
                'Origin' => 'https://pddikti.kemdikbud.go.id',
                'Referer' => 'https://pddikti.kemdikbud.go.id/',
            ]
        ]);

        $this->baseUrl = 'https://pddikti.kemdikbud.go.id';
        // Endpoint API resmi yang digunakan frontend PDDikti
        $this->apiSearchUrl = 'https://api-frontend.kemdikbud.go.id/hit/';
    }

    /**
     * Mencari dosen via API JSON (Metode Utama)
     */
    public function searchDosen(string $namaDosen): array
    {
        $cacheKey = 'pddikti_search_v2_' . md5($namaDosen);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($namaDosen) {
            try {
                // Encode nama untuk URL
                $query = urlencode($namaDosen);
                $url = $this->apiSearchUrl . $query;

                $response = $this->client->request('GET', $url);
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                // Cek apakah ada data 'dosen' di hasil JSON
                if (isset($data['dosen']) && is_array($data['dosen'])) {
                    return $this->formatApiResults($data['dosen']);
                }

                return [];
            } catch (\Exception $e) {
                Log::error('PDDikti API Search Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Format hasil API JSON ke format yang dipakai Controller
     */
    protected function formatApiResults(array $dosenList): array
    {
        $results = [];

        foreach ($dosenList as $dosen) {
            // Format text dari API biasanya: "NAMA DOSEN (NAMA PT - NAMA PRODI)"
            // Contoh: "ADE CHANDRA (UNIVERSITAS XYZ - TEKNIK INFORMATIKA)"
            $rawText = $dosen['text'];

            // Regex untuk memisahkan Nama dan Info PT
            // Pola: Nama (PT - Prodi)
            preg_match('/^(.*?)\s*\((.*?)\s*-\s*(.*?)\)$/', $rawText, $matches);

            $nama = $matches[1] ?? $rawText;
            $pt = $matches[2] ?? '-';
            $prodi = $matches[3] ?? '-';

            // Website-link dari API biasanya: "/data_dosen/HASH_ID"
            $link = $dosen['website-link'] ?? '';

            // Bersihkan link jika ada domain ganda
            if (str_starts_with($link, 'http')) {
                $detailUrl = $link;
            } else {
                $detailUrl = $this->baseUrl . $link;
            }

            $results[] = [
                'nama' => trim($nama),
                'perguruan_tinggi' => trim($pt),
                'program_studi' => trim($prodi),
                'detail_url' => $detailUrl,
                'nidn' => '', // API search pencarian nama tidak selalu return NIDN langsung
            ];
        }

        return $results;
    }

    /**
     * Extract biodata dari halaman detail (Scraping HTML)
     * Bagian ini tetap menggunakan Crawler karena halaman detail biasanya SSR atau static content
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

            // Parsing Data Diri
            $biodata['nama_lengkap'] = $this->extractFieldValue($crawler, 'Nama');
            $biodata['jenis_kelamin'] = $this->extractFieldValue($crawler, 'Jenis Kelamin');
            $biodata['perguruan_tinggi'] = $this->extractFieldValue($crawler, 'Perguruan Tinggi');
            $biodata['program_studi'] = $this->extractFieldValue($crawler, 'Program Studi');
            $biodata['jenjang_pendidikan'] = $this->extractFieldValue($crawler, 'Jenjang Pendidikan');
            $biodata['jabatan_fungsional'] = $this->extractFieldValue($crawler, 'Jabatan Fungsional');
            $biodata['pangkat_golongan'] = $this->extractFieldValue($crawler, 'Pangkat/Golongan'); // Tambahan
            $biodata['status_dosen'] = $this->extractFieldValue($crawler, 'Status Ikatan Kerja');

            // NIDN/NIP seringkali ada di label "NIDN" atau teks dekat nama
            $biodata['nidn'] = $this->extractFieldValue($crawler, 'NIDN') ?? '-';
            $biodata['nip'] = '-'; // Jarang ditampilkan publik
            $biodata['tempat_lahir'] = '-';
            $biodata['tanggal_lahir'] = null;
            $biodata['fakultas'] = '-';
            $biodata['sertifikat_pendidik'] = false; // Default

            // Parsing Tabular Data (Riwayat, Penelitian, dll)
            $biodata['pendidikan'] = $this->extractEducationHistory($crawler);
            $biodata['penelitian'] = $this->extractTableData($crawler, 'Penelitian');
            $biodata['publikasi'] = $this->extractTableData($crawler, 'Publikasi');
            $biodata['pengabdian'] = $this->extractTableData($crawler, 'Pengabdian');

            // Hitung Jumlah
            $biodata['jumlah_penelitian'] = count($biodata['penelitian']);
            $biodata['jumlah_publikasi'] = count($biodata['publikasi']);
            $biodata['jumlah_pengabdian'] = count($biodata['pengabdian']);

            if (empty($biodata['nama_lengkap'])) {
                // Fallback: Coba ambil dari meta tag atau title jika body parsing gagal
                $title = $crawler->filter('title')->text();
                $biodata['nama_lengkap'] = str_replace('PDDikti - ', '', $title);
            }

            return $biodata;
        } catch (\Exception $e) {
            Log::error('Extract biodata error: ' . $e->getMessage());
            return null;
        }
    }

    // --- Helper Extraction Methods (Tetap sama, diperbaiki regex-nya sedikit) ---

    protected function extractFieldValue(Crawler $crawler, string $label): ?string
    {
        try {
            // Cari elemen yang mengandung teks label
            $node = $crawler->filterXPath("//td[contains(text(), '{$label}')] | //div[contains(text(), '{$label}')]");

            if ($node->count() > 0) {
                // Ambil sibling (elemen sebelahnya) atau parent text
                $text = $node->nextAll()->text();
                if (empty($text)) {
                    // Coba ambil dari parent row jika struktur table
                    $text = $node->closest('tr')->filter('td')->last()->text();
                }
                return trim(str_replace(':', '', $text));
            }

            // Fallback: Parsing raw text body
            $bodyText = $crawler->filter('body')->text();
            if (preg_match('/' . $label . '\s*:?\s*(.*?)(?=\n|$)/i', $bodyText, $matches)) {
                return trim($matches[1]);
            }
        } catch (\Exception $e) {
            // Ignore
        }
        return null;
    }

    protected function extractEducationHistory(Crawler $crawler): array
    {
        $data = [];
        try {
            // Cari tab atau section Pendidikan
            $crawler->filter('table.table')->each(function (Crawler $table) use (&$data) {
                if (stripos($table->text(), 'Gelar') !== false || stripos($table->text(), 'Tahun Masuk') !== false) {
                    $table->filter('tbody tr')->each(function ($row) use (&$data) {
                        $cols = $row->filter('td');
                        if ($cols->count() >= 3) {
                            $data[] = [
                                'perguruan_tinggi' => trim($cols->eq(0)->text()),
                                'gelar' => trim($cols->eq(1)->text()),
                                'tahun' => trim($cols->eq(2)->text()),
                            ];
                        }
                    });
                }
            });
        } catch (\Exception $e) {
        }
        return $data;
    }

    protected function extractTableData(Crawler $crawler, string $keyword): array
    {
        $data = [];
        try {
            // PDDikti sering menggunakan tab-content
            $crawler->filter('div.tab-pane, div.card-body')->each(function ($node) use (&$data, $keyword) {
                // Cek apakah section ini judulnya sesuai keyword
                if (stripos($node->html(), $keyword) !== false) {
                    $node->filter('tr')->each(function ($row) use (&$data) {
                        $cols = $row->filter('td');
                        if ($cols->count() >= 2) {
                            $data[] = [
                                'judul' => trim($cols->eq(1)->text()), // Biasanya kolom kedua
                                'tahun' => $cols->count() > 2 ? trim($cols->eq(2)->text()) : '-',
                            ];
                        }
                    });
                }
            });
        } catch (\Exception $e) {
        }
        return $data;
    }

    public function clearCache(string $identifier): void
    {
        Cache::forget('pddikti_search_v2_' . md5($identifier));
    }
}
