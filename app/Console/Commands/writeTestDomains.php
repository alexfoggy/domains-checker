<?php

namespace App\Console\Commands;

use App\DomainToCheck;
use Illuminate\Console\Command;

class writeTestDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:write-test';

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

        $letters = range('A', 'Z');
        foreach ($letters as $first) {
            foreach ($letters as $second) {
                foreach ($letters as $third) {
                    foreach ($letters as $fourth) {
                        $second = strtolower($second);
                        $third = strtolower($third);
                        $first = strtolower($first);
                        $fourth = strtolower($fourth);
                        DomainToCheck::create([
                            'domain' => $first . $second . $third . $fourth . '.ai',
                            'status' => 0
                        ]);
                    }
                }
            }
        }
    }
}
