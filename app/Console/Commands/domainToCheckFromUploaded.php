<?php

namespace App\Console\Commands;

use App\DomainToCheck;
use App\Helpers\Helper;
use Illuminate\Console\Command;

class domainToCheckFromUploaded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domainsuploaded:check';

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
        DomainToCheck::where('status', 0)->chunk(49, function ($domains) {
            Helper::nameCheapCheckFroMUploaded($domains);
        });
    }
}
