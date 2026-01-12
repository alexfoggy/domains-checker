<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainInfo extends Model
{
    protected $guarded = ['id'];

    protected $table = 'domains_info';

    protected $fillable = ['domain','domain_id','last_update','last_parsed','free'];

    public function avalaibleAndVefiriedDomains()
    {
        return $this->hasMany(AvalaibleDomain::class,'domain_id','domain_id')->where('status', 1)->count();
    }
}
