<?php

namespace App\Http\Controllers;

use App\Services\PddiktiScraperService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DosenPddiktiController extends Controller
{
    protected $scraperService;

    public function __construct(PddiktiScraperService $scraperService)
    {
        // Dependency Injection
        $this->scraperService = $scraperService;
    }

    /**
     * API internal untuk mengambil biodata dosen dari PDDikti.
     * @param string $namaDosen
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBiodataByNama(string $namaDosen)
    {
        // Panggil service untuk melakukan scraping
        $data = $this->scraperService->getDosenBiodataByName($namaDosen);

        if (isset($data['error'])) {
            // Kirim respon error ke frontend
            return response()->json(['message' => $data['error']], 404);
        }

        // Kirim data yang sudah di-scrape ke frontend Vue.js
        return response()->json($data);
    }
}
