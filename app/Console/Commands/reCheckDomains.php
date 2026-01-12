<?php

namespace App\Console\Commands;

use App\AvalaibleDomain;
use App\DomainToCheck;
use Illuminate\Console\Command;

class reCheckDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:checkagain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command start checking avalible domains';

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
        $domains = AvalaibleDomain::get();
        if($domains) {
            foreach ($domains as $one_domain) {
                if ($one_domain) {
                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => "https://zozor54-whois-lookup-v1.p.rapidapi.com/nslookup?domain=" . str_replace('https://', '', $one_domain->domain),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => [
                            "X-RapidAPI-Host: zozor54-whois-lookup-v1.p.rapidapi.com",
                            "X-RapidAPI-Key: ea0ffd7fe1mshdcd21e26f662e0ep1c9c0fjsnf393354da2b3",
                        ],
                    ]);

                    $response = curl_exec($curl);

                    if ($response == '{}') {
                        AvalaibleDomain::where('id', $one_domain->id)->update(['status' => 1]);
                    } else {
                        if ($response != '{"message":"You have exceeded the rate limit per minute for your plan, PRO, by the API provider"}') {
                            AvalaibleDomain::where('id', $one_domain->id)->update(['status' => 2]);
                        }
                    }
                    $err = curl_error($curl);

                    curl_close($curl);
                    sleep(6);
                }
            }
        }
    }
}
