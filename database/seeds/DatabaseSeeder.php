<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(DomainToIgnoreSeeder::class);


        // FIll databese with domains to ignore
        if (($open = fopen("domainsToIgnore.csv", "r")) !== FALSE) {

            while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {

                DB::table('domains_to_ignore')->insert([
                    'domain' => $data[1],
                ]);
            }

            fclose($open);
        }
    }
}
