<?php

namespace App\Http\Controllers;

use App\AvalaibleDomain;
use App\Domain;
use App\DomainToCheck;
use App\Helpers\Helper;
use App\Parsing\Parsing;
use Carbon\Carbon;
use Html2Text\Html2Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestController extends Controller
{

    public function index(Request $request)
    {
        $domains = AvalaibleDomain::get();

        if ($domains) {
            foreach ($domains as $one_domain) {
                if ($one_domain) {
                    \App\Helpers\Helper::nameCheapCheck($one_domain);
                }
            }
        }
    }

}
