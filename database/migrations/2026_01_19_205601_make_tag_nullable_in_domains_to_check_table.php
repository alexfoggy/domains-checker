<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeTagNullableInDomainsToCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `domains_to_check` MODIFY COLUMN `tag` VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `domains_to_check` MODIFY COLUMN `tag` VARCHAR(255) NOT NULL DEFAULT 'agency'");
    }
}
