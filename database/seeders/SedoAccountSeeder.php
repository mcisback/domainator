<?php

namespace Database\Seeders;

use App\Models\SedoAccount;
use Illuminate\Database\Seeder;

class SedoAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entries = [
            [
                'name' => 'SEDO me+api@iamattila.com',
                'username' => 'me+api@iamattila.com',
                'password' => 'A9LgEbNzPLS4',
                'partner_id' => '329459',
                'signkey' => '124db41b89c77b1252039669b9bd2a',
            ]
        ];

        foreach ($entries as $entry) {
            SedoAccount::create($entry);
        }
    }
}
