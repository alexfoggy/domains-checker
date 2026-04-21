<?php

namespace App\Services\Hunter;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HunterClient
{
    private $baseUrl = 'https://api.hunter.io';
    private $apiKey;

    public function __construct(
        string $apiKey,
        string $baseUrl = 'https://api.hunter.io'
    ) {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    public static function fromConfig(): self
    {
        $key = (string) config('services.hunter.api_key', '');
        if ($key === '') {
            throw new \RuntimeException('Hunter.io API key is missing. Set HUNTER_IO_API_KEY in your environment.');
        }

        return new self(
            $key,
            rtrim((string) config('services.hunter.base_url', 'https://api.hunter.io'), '/')
        );
    }

    /**
     * Create a lead or update an existing one (matched by email).
     *
     * @see https://hunter.io/api-documentation/v2#create-or-update-a-lead
     *
     * @param  array<string, mixed>  $lead
     */
    public function upsertLead(array $lead): Response
    {
        $url = $this->baseUrl . '/v2/leads?' . http_build_query(['api_key' => $this->apiKey]);
        dd($url);
        return Http::timeout((int) config('services.hunter.timeout', 30))
            ->acceptJson()
            ->asJson()
            ->put($url, $lead);
    }

    /**
     * @return array<string, mixed>
     */
    public static function optionalLeadsListPayloadFromConfig(): array
    {
        $id = config('services.hunter.leads_list_id');
        if ($id === null || $id === '') {
            return [];
        }

        return ['leads_list_id' => (int) $id];
    }
}
