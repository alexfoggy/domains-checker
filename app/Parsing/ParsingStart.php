<?php

namespace App\Parsing;

use App\Domain;
use Carbon\Carbon;

class ParsingStart
{
    public function parsing()
    {
        $domain = Domain::orderBy('updated_at', 'ASC')->first();
        if ($domain) {

            $domain->updated_at = Carbon::now();

            $domain->save();
            $parsing = new Parsing($domain);

            $parsing->startParsing();

        }

    }
}
