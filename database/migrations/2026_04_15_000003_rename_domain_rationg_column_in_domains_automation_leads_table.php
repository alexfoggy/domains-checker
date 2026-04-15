<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameDomainRationgColumnInDomainsAutomationLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (
            Schema::hasTable('domains_automation_leads')
            && Schema::hasColumn('domains_automation_leads', 'domain_rationg')
            && !Schema::hasColumn('domains_automation_leads', 'domain_raiting')
        ) {
            DB::statement('ALTER TABLE domains_automation_leads CHANGE domain_rationg domain_raiting VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (
            Schema::hasTable('domains_automation_leads')
            && Schema::hasColumn('domains_automation_leads', 'domain_raiting')
            && !Schema::hasColumn('domains_automation_leads', 'domain_rationg')
        ) {
            DB::statement('ALTER TABLE domains_automation_leads CHANGE domain_raiting domain_rationg VARCHAR(255) NULL');
        }
    }
}
