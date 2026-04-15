<?php

namespace App\UseCases;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GetAhrefsRatingUseCase
{
    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public static function execute(string $domain): array
    {
        $apiKey = config('services.ahrefs.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('AHREFS_API_KEY is not configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json'
        ])->get('https://api.ahrefs.com/v3/site-explorer/domain-rating', [
            'protocol' => 'https',
            'target' => $domain,
            'date' => now()->format('Y-m-d'),
            'output' => 'json',
        ])->throw()
            ->json();

        $rating = $response['domain_rating']['domain_rating'] ?? null;

        if ($rating === null) {
            throw new RuntimeException('Invalid Ahrefs response: domain rating missing.');
        }

        return [
            'domain_raiting' => $rating,
        ];
    }
}
