<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsAutomationLeadEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_automation_lead_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('domain_automation_lead_id');
            $table->string('email')->unique();
            $table->boolean('is_hunder_lead_created')->default(false);
            $table->timestamps();

            $table->foreign('domain_automation_lead_id')
                ->references('id')
                ->on('domains_automation_leads')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains_automation_lead_emails');
    }
}
