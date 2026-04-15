<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainAutomationLead extends Model
{
    protected $guarded = ['id'];

    protected $table = 'domains_automation_leads';

    protected $fillable = [
        'domain',
        'domain_raiting',
        'is_hunter_synced',
    ];

    public function emails()
    {
        return $this->hasMany(DomainAutomationLeadEmail::class, 'domain_automation_lead_id');
    }
}
