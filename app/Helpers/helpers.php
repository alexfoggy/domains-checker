<?php


namespace App\Helpers;

use App\AvalaibleDomain;
use App\DomainToCheck;
use Illuminate\Support\Collection;

class Helper
{
    public static function replaceForDomain($domain, $status = false)
    {

        $domain = str_replace('https://', '', $domain);
        $domain = str_replace('www.', '', $domain);
        $domain = str_replace('/', '', $domain);

        $domainArray = explode('.', $domain);

        $return = '';
        if (count($domainArray) > 2) {
            if ($status) {
                $return = '' . $domainArray[count($domainArray) - 3] . '.' . $domainArray[count($domainArray) - 2] . '.' . end($domainArray);
            } else {
                $return = '' . $domainArray[count($domainArray) - 2] . '.' . end($domainArray);
            }
        }

        return $domain;
    }

    public static function checkIfMoreThanTwo($domain)
    {

        $domain = explode('.', $domain);
        if (count($domain) > 2) {
            return true;
        }

        return false;
    }

    public static function hasRightEnd($domain)
    {
        $domain = explode('.', $domain);

        $domain_end = end($domain);

        $array = ['shtml', 'html', 'htm', 'phtml', 'txt', 'asp', 'aspx'];

        if (in_array($domain_end, $array)) {
            return false;
        }
        return true;

    }

    public static function nameCheapCheck($domain, $checkSecondTime = false)
    {
        var_dump($domain->domain);
        $domainName = Helper::replaceForDomain($domain->domain, $checkSecondTime);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.namecheap.com/xml.response?');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
//                "ApiUser=muzikk1&ApiKey=3030ac784d204484a431328244e5206f&UserName=muzikk1&Command=namecheap.domains.check&ClientIp=178.168.61.197&DomainList=".$domain);
            "ApiUser=domainboxx&ApiKey=3ef3399d09e043609a6dced679232bd5&UserName=domainboxx&Command=namecheap.domains.check&ClientIp=217.76.55.111&DomainList=" . $domainName);
//            "ApiUser=domainboxx&ApiKey=3ef3399d09e043609a6dced679232bd5&UserName=domainboxx&Command=namecheap.domains.check&ClientIp=217.76.55.111&DomainList=" . $domainName);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($server_output);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

        var_dump($array);
        if (!$array['Errors']) {
            if ($array['CommandResponse']['DomainCheckResult']['@attributes']['Available'] == "true") {
                AvalaibleDomain::where('id', $domain->id)->update(['status' => 1]);
                if ($array['CommandResponse']['DomainCheckResult']['@attributes']['IsPremiumName'] == "true") {
                    AvalaibleDomain::where('id', $domain->id)->update(['is_premium' => 1]);
                }
                var_dump('good domain');
            } else {
                AvalaibleDomain::where('id', $domain->id)->update(['status' => 2]);
                var_dump('bad one');
            }
            sleep(6);
        } else {
            AvalaibleDomain::where('id', $domain->id)->update(['status' => 2]);
            sleep(3);
        }
    }

    public static function nameCheapCheckFroMUploaded(Collection $domains, $checkSecondTime = false)
    {
        $clearedDomains = [];

        foreach ($domains as $domain) {
            $clearedDomains[] = Helper::replaceForDomain($domain->domain, $checkSecondTime);
        }

        $domainsInList = implode(',', $clearedDomains);
        $domainsInList = str_replace("\r", '', $domainsInList);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.namecheap.com/xml.response?');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "ApiUser=domainboxx&ApiKey=3ef3399d09e043609a6dced679232bd5&UserName=domainboxx&Command=namecheap.domains.check&ClientIp=217.76.55.111&DomainList=" . $domainsInList);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($server_output);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

        dd($array, $clearedDomains);
        if ($array['CommandResponse']['DomainCheckResult']) {
            foreach ($array['CommandResponse']['DomainCheckResult'] as $row) {
                if ($row['@attributes']['Available'] == "true") {
                    if ($row['@attributes']['IsPremiumName'] == "true") {
                        DomainToCheck::where('domain', 'LIKE', '%' . $row['@attributes']['Domain'] . '%')->update(['status' => 2]);
                    } else {
                        var_dump('good domain ' . $row['@attributes']['Domain']);
                        DomainToCheck::where('domain', 'LIKE', '%' . $row['@attributes']['Domain'] . '%')->update(['status' => 1]);
                    }
                } else {
                    DomainToCheck::where('domain', 'LIKE', '%' . $row['@attributes']['Domain'] . '%')->update(['status' => 2]);
                }
            }
        }

        if ($array['Errors']) {
            if (!empty($array['Errors']['Error'])) {
                foreach ($array['Errors']['Error'] as $error) {
                    $domainFromError = explode("'", $error);
                    var_dump($domainFromError);
                    if (count($domainFromError) > 1) {
                        DomainToCheck::where('domain', 'LIKE', '%' . $domainFromError[1] . '%')->update(['status' => 2]);
                    }
                }
            }
        }

        sleep(5);
    }

    public static function cleanDomain(string $line): string
    {
        $substrings_to_remove = [
            "http://www2.",
            "http://www.",
            "http://",
            "https://www.",
            "https://",
            "https",
            "http",
            "www"
        ];

        // Remove all unwanted substrings
        $line = str_replace($substrings_to_remove, "", $line);

        // If there's a /, take only the part before the first one
        if (str_contains($line, '/')) {
            $line = explode('/', $line)[0];
        }

        return trim($line);
    }
}

