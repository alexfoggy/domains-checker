<?php

namespace App\Console\Commands;

use App\AvalaibleDomain;
use App\Helpers\Helper;
use Illuminate\Console\Command;

class checkDomainsNameCheapNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'namecheap:checknew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command start checking again avalible domains';

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
        $domains = AvalaibleDomain::where('status', 0)->orderBy('updated_at', 'DESC')->get();

        if ($domains) {
            foreach ($domains as $one_domain) {
                if ($one_domain) {
                    Helper::nameCheapCheck($one_domain);
                }
            }
        }
    }
}
