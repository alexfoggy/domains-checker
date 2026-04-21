<?php

namespace App\Console\Commands;

use App\DomainAutomationLead;
use App\DomainAutomationLeadEmail;
use App\UseCases\FindHunterEmailByDomainUseCase;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetHunterEmailsFromDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hunter:get-emails-from-domains';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Emails from domains';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle()
    {
        $domains = DomainAutomationLead::where('is_hunter_synced', false)->get();

        $this->info('Total domains to process: ' . $domains->count());

        foreach ($domains as $domain) {
            DB::beginTransaction();
            try {
                $emails = FindHunterEmailByDomainUseCase::execute($domain->domain);

                $this->info('Fetched Emails for ' . $domain->domain);

                foreach ($emails as $email) {
                    DomainAutomationLeadEmail::create([
                        'domain_automation_lead_id' => $domain->id,
                        'email' => $email
                    ]);
                }

                $domain->is_hunter_synced = true;
                $domain->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $this->error('Error fetching Emails for ' . $domain->domain . ': ' . $e->getMessage());
                Log::error('Error fetching Ahrefs rating for ' . $domain->domain . ': ' . $e->getMessage());
            }
        }

        return 0;
    }
}
