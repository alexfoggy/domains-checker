<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_info', function (Blueprint $table) {
            $table->id();
            $table->string('domain');
            $table->integer('domain_id');
            $table->dateTime('last_update');
            $table->dateTime('last_parsed');
            $table->integer('free');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains_info');
    }
}
