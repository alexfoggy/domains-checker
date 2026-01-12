<?php

namespace App\Console\Commands;

use App\Domain;
use App\Parsing\Parsing;
use Carbon\Carbon;
use Illuminate\Console\Command;

class startParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:start';

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
	    $domain = Domain::orderBy('updated_at', 'ASC')->first();
        Domain::where('id', $domain->id)->update(['updated_at' => Carbon::now()]);
        var_dump($domain->domain);
        $parsing = new Parsing($domain);
        $parsing->startParsing();
    }
}
