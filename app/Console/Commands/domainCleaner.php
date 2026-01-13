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
        $domains = DomainToCheck::all()->each(function ($item) {
            $item->domain = trim($item->domain);
        });
        $count = 0;
        $domains->groupBy('domain')->filter(function ($item) use ($count) {
            if ($item->count() > 1) {
                $count++;
                $item->last()->delete();
            }
        });

        var_dump($count. ' domains removed');
    }
}
