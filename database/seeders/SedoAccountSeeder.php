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
                'username' => 'APIxIamcorp',
                'password' => 'A9LgEbNzPLS4',
                'partner_id' => '329459',
                'signkey' => '124db41b89c77b1252039669b9bd2a',
                'domain_ownership_id' => 'edfda153b169e25d4f8ee3b05210a9da735e1ed6',
            ]
        ];

        foreach ($entries as $entry) {
            SedoAccount::create($entry);
        }
    }
}
