<?php

namespace App\UseCases;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FindHunterEmailByDomainUseCase
{
    private const BASE_URL = 'https://api.hunter.io/v2';

    /** @var list<string> */
    private const FIRST_NAMES = ['marketing', 'info', 'promo'];

    /**
     * Calls Hunter Email Finder once per first_name (marketing, info, promo) and returns merged unique emails.
     *
     * @param string $domain
     * @return array
     */
    public static function execute(string $domain): array
    {
        $apiKey = config('services.hunter.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('HUNTER_API_KEY is not configured.');
        }

        $emails = [];

        foreach (self::FIRST_NAMES as $firstName) {
            $email = self::fetchEmailForFirstName($domain, $apiKey, $firstName);

            if ($email !== null && $email !== '' && !in_array($email, $emails, true)) {
                $emails[] = $email;
            }
        }

        return $emails;
    }

    private static function fetchEmailForFirstName(string $domain, string $apiKey): ?string
    {
        $response = Http::acceptJson()->get(self::BASE_URL . '/domain-search', [
            'domain' => $domain,
            'api_key' => $apiKey,
        ]);

        if ($response->status() === 404) {
            return null;
        }

        if (!$response->successful()) {
            $message = self::formatHunterError($response->json(), $response->body());

            throw new RuntimeException('Hunter API request failed: ' . $message);
        }

        $payload = $response->json();
        $data = is_array($payload) ? ($payload['data'] ?? null) : null;
        return is_array($data) ? ($data['emails'] ?? null) : null;
    }

    /**
     * @param  array<string, mixed>|null  $json
     */
    private static function formatHunterError(?array $json, string $fallbackBody): string
    {
        if (!is_array($json) || !isset($json['errors'])) {
            return $fallbackBody !== '' ? $fallbackBody : 'unknown error';
        }

        $errors = $json['errors'];

        if (is_array($errors) && isset($errors[0]) && is_array($errors[0])) {
            $first = $errors[0];
            $details = $first['details'] ?? $first['id'] ?? null;

            if (is_string($details) && $details !== '') {
                return $details;
            }
        }

        return json_encode($errors) ?: $fallbackBody;
    }
}
