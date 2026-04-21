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

        $emailsToSend = DomainAutomationLeadEmail::where('is_hunder_lead_created', false)
            ->get();

        if ($emailsToSend->isEmpty()) {
            $this->info('No new emails to send to Hunter.io.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Processing %d emails(s).', $emailsToSend->count()));

        $ok = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($emailsToSend as $email) {

            $compaignId = $this->getCompaignIdForEmail($email);

            if (!$compaignId) {
                $skipped++;
                $email->is_hunder_lead_created = true;
                $email->save();
                continue;
            }

            $response = $client->addCampaignRecipient($email->email, $compaignId);

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

            if ($response->status() === 429) {
                $this->error('Hunter.io rate limit reached. Increase --sleep and retry.');
                break;
            }
        }

        $this->info(sprintf('Done. Success: %d, Failed: %d. Skipped: %d.', $ok, $failed, $skipped));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function getCompaignIdForEmail(DomainAutomationLeadEmail $email): ?string
    {
        switch ($email->domainAutomationLead->domain_raiting) {
            case null:
            case $email->domainAutomationLead->domain_raiting >= 0 &&
                $email->domainAutomationLead->domain_raiting < 5:
                return config('services.hunter.campaigns.one');
            case $email->domainAutomationLead->domain_raiting >= 5 &&
                $email->domainAutomationLead->domain_raiting < 10:
                return config('services.hunter.campaigns.two');
            case $email->domainAutomationLead->domain_raiting >= 10 &&
                $email->domainAutomationLead->domain_raiting < 15:
                return config('services.hunter.campaigns.three');
            case $email->domainAutomationLead->domain_raiting >= 15 &&
                $email->domainAutomationLead->domain_raiting < 20:
                return config('services.hunter.campaigns.four');
            case $email->domainAutomationLead->domain_raiting >= 20 &&
                $email->domainAutomationLead->domain_raiting < 25:
                return config('services.hunter.campaigns.five');
            case $email->domainAutomationLead->domain_raiting >= 25 &&
                $email->domainAutomationLead->domain_raiting < 30:
                return config('services.hunter.campaigns.six');
            case $email->domainAutomationLead->domain_raiting >= 30 &&
                $email->domainAutomationLead->domain_raiting < 35:
                return config('services.hunter.campaigns.seven');
            case $email->domainAutomationLead->domain_raiting >= 35 &&
                $email->domainAutomationLead->domain_raiting < 40:
                return config('services.hunter.campaigns.eight');
            default:
                return null;
        }

    }
}
