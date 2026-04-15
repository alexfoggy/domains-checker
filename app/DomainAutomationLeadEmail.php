<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainAutomationLeadEmail extends Model
{
    protected $guarded = ['id'];

    protected $table = 'domains_automation_lead_emails';

    protected $fillable = [
        'domain_automation_lead_id',
        'email',
        'is_hunder_lead_created',
    ];

    public function domainAutomationLead()
    {
        return $this->belongsTo(DomainAutomationLead::class, 'domain_automation_lead_id');
    }
}
