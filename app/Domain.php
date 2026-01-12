<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $guarded = ['id'];

    public function parsedLinksCount()
    {
        return $this->hasMany(ParsedLink::class)->where('parsed', 1)->count();
    }

    public function TotalLinksCount()
    {
        return $this->hasMany(ParsedLink::class)->count();
    }

    public function lastParsedLink(){
        return $this->hasOne(ParsedLink::class,'domain_id','id')->orderBy('created_at','DESC')->first();
    }

    public function avalaibleDomainsCount()
    {
        return $this->hasMany(AvalaibleDomain::class)->count();
    }

    public function parsedLinks(){
        return $this->hasMany(ParsedLink::class);
    }

    public function avalaibleDomains()
    {
        return $this->hasMany(AvalaibleDomain::class);
    }
    public function avalaibleAndVefiriedDomains()
    {
        return $this->hasMany(AvalaibleDomain::class)->where('status', 1)->count();
    }
}
