<?php

namespace App\Services;

use App\Models\Page;
use Exception;
use Illuminate\Support\Str;

class RecommendationService
{
    protected $googleSearchService;

    public function __construct(GoogleSearchService $googleSearchService)
    {
        $this->googleSearchService = $googleSearchService;
    }

    public function getRecommendations($limit = 5)
    {
        try {
            $website = Page::all()->random();
            $searchQuery = $website->title . ' ' . Str::limit($website->description, 100);
        } catch (Exception $e) {
            $searchQuery = 'Indonesia berita terbaru penyandang disabilitas';
        }
        $googleResults = $this->googleSearchService->search($searchQuery, $limit);

        return array_slice($googleResults, 0, $limit, true);

        // $recommendations = $this->compareWithDatabase($googleResults);

    }

    private function compareWithDatabase($googleResults)
    {
        $recommendations = [];

        foreach ($googleResults as $result) {
            $similarWebsite = Page::where('url', 'like', '%' . parse_url($result['link'], PHP_URL_HOST) . '%')->first();

            if ($similarWebsite) {
                $similarity = $this->calculateSimilarity($result, $similarWebsite);
                $recommendations[$similarWebsite->id] = [
                    'website' => $similarWebsite,
                    'similarity' => $similarity,
                    'google_rank' => count($recommendations) + 1
                ];
            }
        }

        uasort($recommendations, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        return $recommendations;
    }

    private function calculateSimilarity($googleResult, $databaseWebsite)
    {
        $googleTokens = $this->tokenize($googleResult['title'] . ' ' . ($googleResult['snippet'] ?? ''));
        $databaseTokens = $this->tokenize($databaseWebsite->title . ' ' . $databaseWebsite->description);

        return $this->calculateCosineSimilarity($googleTokens, $databaseTokens);
    }

    private function tokenize($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        return array_count_values(str_word_count($text, 1));
    }

    private function calculateCosineSimilarity($tokens1, $tokens2)
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        foreach ($tokens1 as $token => $count) {
            $magnitude1 += $count * $count;
            if (isset($tokens2[$token])) {
                $dotProduct += $count * $tokens2[$token];
            }
        }

        foreach ($tokens2 as $count) {
            $magnitude2 += $count * $count;
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 && $magnitude2) {
            return $dotProduct / ($magnitude1 * $magnitude2);
        }

        return 0;
    }
}
