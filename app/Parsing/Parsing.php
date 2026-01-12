<?php

namespace App\Parsing;

use App\AvalaibleDomain;
use App\Domain;
use App\DomainToIgnore;
use App\ParsedLink;
use Html2Text\Html2Text;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Parsing
{
    private $domain;
    private $urls = array();
    private $index = 0;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    function isValidUrl($url)
    {

        if (!$url || !is_string($url)) {

            return false;

        }

        if ($this->getHttpResponseCode_using_curl($url) == 0) {
            return false;
        }
        return true;
    }

    function getHttpResponseCode_using_curl($url, $followredirects = true)
    {
        if (!$url || !is_string($url)) {
            return false;
        }
        $ch = @curl_init($url);
        if ($ch === false) {
            return false;
        }
        @curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        @curl_setopt($ch, CURLOPT_NOBODY, false);    // dont need body
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // catch output (do NOT print!)
        if ($followredirects) {
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            @curl_setopt($ch, CURLOPT_MAXREDIRS, 5);  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
        } else {
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        }
        @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);   // fairly random number (seconds)... but could prevent waiting forever to get a result
        @curl_setopt($ch, CURLOPT_TIMEOUT, 15);   // fairly random number (seconds)... but could prevent waiting forever to get a result
        @curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1");   // pretend we're a regular browser
        @curl_exec($ch);

        if (@curl_errno($ch)) {
            return @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        $code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); // note: php.net documentation shows this returns a string, but really it returns an int
        @curl_close($ch);
        return $code;
    }

    private function addParsedLink($link, $parsed = 0)
    {
        if (!$this->domain->parsedLinks()->where('link', $link)->first()) {
            $this->domain->parsedLinks()->updateOrCreate(
                ['link' => $link],
                ['parsed' => $parsed]
            );
        }
    }


    public function startParsing()
    {
        $this->addParsedLink($this->domain->domain);
        var_dump($this->domain->domain);
        $this->extractAllLinks();
    }

//    public function isValidDomainName($domain_name)
//    {
//        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
//            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
//            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)); //length of each label
//    }

    private function extractAllLinks()
    {
        //Extragem primul url ne parsat
        $unParsedLink = ParsedLink::where('domain_id', $this->domain->id)->where('parsed', 0)->orderBy('created_at')->first();
        if (!$unParsedLink) {
            return false;
        }
        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));
        $html = @file_get_contents($unParsedLink->link, false, $context);
        if ($html) {
            //Create a new DOM documentl
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $links = $dom->getElementsByTagName('a');
            foreach ($links as $link) {
                $link = rtrim($link->getAttribute('href'), '/');
                if (str_contains($link, '.')) {
                    if (substr($link, 0, 1) == '/' || substr($link, 0, 1) == '?') {
                        $link = $this->domain->domain . $link;
                    }

                    //Replace http with https

                    if (substr($link, 0, strlen('http://')) == 'http://') {

                        $link = str_replace('http://', 'https://', $link);

                    }

                    //Set https if is not

                    if (substr($link, 0, strlen('https://')) != 'https://') {

                        $link = 'https://' . $link;

                    }

                    if (!preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $link)) {
                        continue;
                    }
                    //Check for existing link in database
                    if (!$this->domain->parsedLinks()->where('link', $link)->first()) {
                        if (Str::contains($link, $this->domain->same_site)) {
                            var_dump('Same site Link');
                            $this->addParsedLink($link, 0);
                        } else {
                            $parse = parse_url($link);
                            var_dump($parse);
                            if (!$this->isValidUrl($parse['scheme'] . '://' . $parse['host'])) {
                                var_dump('error 1');
                                if (strlen($parse['host']) < 255) {
//                                    $parsedDomain = str_replace('www.', '', $parse['host']);
//                                    if ($parsedDomain) {
                                    var_dump('error 2');

                                    if ($this->checkIfNotInList($parse['host'])) {
                                        var_dump('URAAAAAAAAAAAAAAAAAAAa');
                                        $domain = $this->domain->avalaibleDomains()->firstOrCreate(
                                            ['domain' => $parse['scheme'] . '://' . $parse['host']],
                                            ['from' => $unParsedLink->link]
                                        );
                                        var_dump($domain);
//
//                                        $thisDomain = AvalaibleDomain::latest()->first();
//
//                                        $curl = curl_init();
//
//                                        curl_setopt_array($curl, [
//                                            CURLOPT_URL => "https://zozor54-whois-lookup-v1.p.rapidapi.com/nslookup?domain=" . $thisDomain->domain,
//                                            CURLOPT_RETURNTRANSFER => true,
//                                            CURLOPT_FOLLOWLOCATION => true,
//                                            CURLOPT_ENCODING => "",
//                                            CURLOPT_MAXREDIRS => 10,
//                                            CURLOPT_TIMEOUT => 30,
//                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                                            CURLOPT_CUSTOMREQUEST => "GET",
//                                            CURLOPT_HTTPHEADER => [
//                                                "X-RapidAPI-Host: zozor54-whois-lookup-v1.p.rapidapi.com",
//                                                "X-RapidAPI-Key: ea0ffd7fe1mshdcd21e26f662e0ep1c9c0fjsnf393354da2b3",
//                                            ],
//                                        ]);
//
//                                        $response = curl_exec($curl);
//
//                                        if (empty($response)) {
//                                            AvalaibleDomain::where('id', $thisDomain->id)->update(['status' => 1]);
//                                        } else {
//                                            AvalaibleDomain::where('id', $thisDomain->id)->update(['status' => 2]);
//                                        }
//                                        sleep(5);
//                                        $err = curl_error($curl);
//
//                                        curl_close($curl);
                                    }
                                }

                            }
                        }
                    }
                }
            }
            $unParsedLink->parsed = 1;
            $unParsedLink->save();
            $this->addParsedLink($unParsedLink->link, 1);
            unset($dom);

        }
        $unParsedLink->parsed = 1;
        $unParsedLink->save();
        $this->addParsedLink($unParsedLink->link, 1);
        unset($dom);
        $this->extractAllLinks();
    }

    public function checkIfNotInList($domain)
    {
        $array = DomainToIgnore::get()->pluck('domain')->toArray();
        $response = true;
        if (in_array($domain, $array)) {
            $response = false;
        }
        return $response;
    }
}
