<?php

namespace App\Parsing;

use App\AvalaibleDomain;
use App\Domain;
use App\ParsedLink;
use Carbon\Carbon;
use Html2Text\Html2Text;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckDomainsStatus
{
    public function startCheckDomains(){
        {
            $domainsToCheck = AvalaibleDomain::where('status',0)->get();
            if($domainsToCheck) {
                foreach ($domainsToCheck as $oneDomain) {

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => "https://zozor54-whois-lookup-v1.p.rapidapi.com/nslookup?domain=" . $oneDomain->domain,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => [
                            "X-RapidAPI-Host: zozor54-whois-lookup-v1.p.rapidapi.com",
                            "X-RapidAPI-Key: ".env('X-KEY')
                        ],
                    ]);

                    $response = curl_exec($curl);

                    if (empty($response)) {
                        AvalaibleDomain::where('id', $oneDomain->id)->update(['status' => 1]);
                    } else {
                        AvalaibleDomain::where('id', $oneDomain->id)->update(['status' => 2]);
                    }

                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                        echo "cURL Error #:" . $err;
                    } else {
                        echo $response;
                    }
                }
            }
        }
    }

}
