<?php

namespace App\Console\Commands;

use App\Domain;
use App\DomainInfo;
use App\Parsing\Parsing;
use Carbon\Carbon;
use Illuminate\Console\Command;

class domainsInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:getInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Staring parse proccess';

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
        $domains = Domain::get();
        foreach ($domains as $one_domain) {
            var_dump($one_domain->domain);
            DomainInfo::updateOrCreate([
                'domain_id' =>$one_domain->id
            ],[
                'domain'=>$one_domain->domain,
                'last_update'=>now(),
                'last_parsed'=>$one_domain->lastParsedLink() ? $one_domain->lastParsedLink()->created_at : null,
                'free'=>$one_domain->avalaibleAndVefiriedDomains(),
            ]);

        }
    }
}
