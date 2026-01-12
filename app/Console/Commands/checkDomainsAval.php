<?php

namespace App\Console\Commands;

use App\AvalaibleDomain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Helper;

class checkDomainsAval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domainsaval:check';

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
        $domains = AvalaibleDomain::where('status', '1')->get();
        if ($domains) {
            $arr = [];
            foreach ($domains as $one_domain) {
                if ($one_domain) {
                    $result = $this->check($one_domain);
                    if($result){
                        array_push($arr,$result);
                    }
                }
            }
        }

    }

    public function check($one_domain, $checkSecondTime = false)
    {
        $domain = \App\Helpers\Helper::replaceForDomain($one_domain->domain,$checkSecondTime);
        $resName = '';
        if (\App\Helpers\Helper::hasRightEnd($domain)) {

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://zozor54-whois-lookup-v1.p.rapidapi.com/nslookup?domain=" . $domain,
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

            $checkStatus = true;

            if ($response == '{}') {
                AvalaibleDomain::where('id', $one_domain->id)->update(['status' => 1]);

                $checkStatus = false;
                var_dump(true, $domain);
                $resName = $one_domain->domain;
            } else {
                if ($response != '{"message":"You have exceeded the rate limit per minute for your plan, PRO, by the API provider"}') {
                    AvalaibleDomain::where('id', $one_domain->id)->update(['status' => 2]);
                    var_dump(false, $domain);
                }
            }
            $err = curl_error($curl);

            curl_close($curl);

//            if ($checkStatus && !$checkSecondTime && \App\Helpers\Helper::checkIfMoreThanTwo($one_domain->domain)) {
//                $this->check($one_domain, true);
//                var_dump('check second time', $domain,$one_domain->domain);
//            }

            sleep(6);
            return $resName;
        }
        else {
            AvalaibleDomain::where('id', $one_domain->id)->update(['status' => 2]);
        }
        return $resName;
    }
}
