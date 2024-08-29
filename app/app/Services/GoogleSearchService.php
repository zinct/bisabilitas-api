<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleSearchService
{
    protected $apiKey;
    protected $searchEngineId;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_API_KEY');
        $this->searchEngineId = env('GOOGLE_SEARCH_ENGINE_ID');
    }

    public function search($query, $limit = 10)
    {
        $response = Http::get('https://www.googleapis.com/customsearch/v1', [
            'key' => $this->apiKey,
            'cx' => $this->searchEngineId,
            'q' => $query,
            'num' => $limit
        ]);

        if ($response->successful()) {
            $items = $response->json()['items'] ?? [];
            $results = [];

            foreach ($items as $item) {
                $title = $item['title'] ?? null;
                $description = $item['snippet'] ?? null;

                // Cek apakah ada pagemap dan apakah terdapat cse_image untuk mendapatkan gambar
                $image = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRKREcZ7HHA4nPfRJwXkrv0-i11G3uaxIGZVA&s';
                if (isset($item['pagemap']['cse_image'])) {
                    $image = $item['pagemap']['cse_image'][0]['src'] ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRKREcZ7HHA4nPfRJwXkrv0-i11G3uaxIGZVA&s';
                }

                $results[] = [
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'url' => $item['link'],
                ];
            }

            return $results;
        }

        throw new \Exception('Google Search API request failed');
    }
}
