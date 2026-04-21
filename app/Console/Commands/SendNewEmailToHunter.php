<?php

namespace App\Console\Commands;

use App\DomainAutomationLeadEmail;
use App\Services\Hunter\HunterClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SendNewEmailToHunter extends Command
{
    protected $signature = 'hunter:send-new-email-to-hunter';

    public function handle(): int
    {
        try {
            $client = HunterClient::fromConfig();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $listPayload = HunterClient::optionalLeadsListPayloadFromConfig();

        $emailsToSend = DomainAutomationLeadEmail::where('is_hunder_lead_created', false)
            ->get();

        if ($emailsToSend->isEmpty()) {
            $this->info('No new emails to send to Hunter.io.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Processing %d emails(s).', $emailsToSend->count()));

        $ok = 0;
        $failed = 0;

        foreach ($emailsToSend as $email) {
            $payload = array_merge(
                [
                    'first_name' => 'Hi',
                    'last_name' => 'there',
                    'email' => $email->email,
                ],
                $listPayload
            );

            $response = $client->upsertLead($payload);

            if ($response->successful()) {
                $ok++;
                $email->is_hunder_lead_created = true;
                $email->save();
                continue;
            }

            $failed++;
            Log::warning('Hunter.io lead upsert failed', [
                'email' => $email->email,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            $this->warn(sprintf(
                'Failed user id=%d email=%s HTTP %s',
                [
                    $email->email,
                    (string)$response->status()
                ]
            ));

            if ($response->status() === 429) {
                $this->error('Hunter.io rate limit reached. Increase --sleep and retry.');
                break;
            }
        }

        $this->info(sprintf('Done. Success: %d, failed: %d.', $ok, $failed));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
