<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleImageSearchService
{
    protected $apiKey;
    protected $searchEngineId;

    public function __construct()
    {
        // $this->apiKey = config('services.google.api_key');
        $this->apiKey = 'AIzaSyBQDOA8i0zzmDe4QyiThFctMxQNeftu2aE';
        // $this->searchEngineId = config('services.google.search_engine_id');
        $this->searchEngineId = '3532036a93a7d4c5a';
    }

    public function searchImages($query, $limit = 2)
    {
        $response = Http::get('https://www.googleapis.com/customsearch/v1', [
            'key' => $this->apiKey,
            'cx' => $this->searchEngineId,
            'q' => $query,
            'searchType' => 'image',
            'num' => $limit,
        ]);

        if ($response->successful()) {
            return $response->json()['items'] ?? [];
        }

        return [];
    }
}
