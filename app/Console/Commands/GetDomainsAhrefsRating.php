<?php

namespace App\Console\Commands;

use App\DomainAutomationLead;
use App\UseCases\GetAhrefsRatingUseCase;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetDomainsAhrefsRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ahrefs:get-domains-rating';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Ahrefs domain rating for automation leads';

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
        $domains = DomainAutomationLead::whereNull('domain_raiting')->get();

        $this->info('Total domains to process: ' . $domains->count());

        foreach ($domains as $domain) {
            try {
                $domainRating = GetAhrefsRatingUseCase::execute($domain->domain);

                $this->info('Fetched Ahrefs rating for ' . $domain->domain . ': ' . $domainRating['domain_raiting']);

                $domain->domain_raiting = $domainRating['domain_raiting'];
                $domain->save();
            } catch (Exception $e) {
                Log::error('Error fetching Ahrefs rating for ' . $domain->domain . ': ' . $e->getMessage());
                $this->error('Error fetching Ahrefs rating for ' . $domain->domain . ': ' . $e->getMessage());
            }
        }

        return 0;
    }
}
