<?php

namespace App\Console\Commands;

use App\DomainToCheck;
use Illuminate\Console\Command;

class domainCleaner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command start removing double domains';

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
        $doublicates = collect();
        $domains = DomainToCheck::all();
        $domains->groupBy('domain');
        foreach ($domains as $domain_group) {
            if ($domain_group->count() > 1) {
                $doublicates->push($domain_group);
            }
        }

        dd($doublicates->pluck('domain'));
    }
}
