<?php

namespace App\Console\Commands;

use App\DomainToCheck;
use Illuminate\Console\Command;

class domainVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command start verifying domains';

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
     */
    public function handle()
    {
        $results = collect();
        $domains = DomainToCheck::all()->each(function ($item) {
            $item->domain = trim($item->domain);
        });

        foreach ($domains as $domain) {
            // need check if domain is really domain, not text
            if (count(explode('.', $domain->domain)) < 2) {
                $results->push($domain);
            }
        }

        dd($results);
    }
}
