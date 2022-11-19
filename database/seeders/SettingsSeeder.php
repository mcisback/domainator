<?php

namespace Database\Seeders;

use App\Models\SedoAccount;
use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
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
                'key' => 'namecheap',
                'value' => [
                    'ApiUser' => 'demarkmark',
                    'ApiKey' => 'ced40fb53f3645b0b790fb17b16cf775',
                    'RegistrantFirstName' => 'Attila',
                    'RegistrantLastName' => 'Odri',
                    'RegistrantAddress1' => 'Jozsef korut 34',
                    'RegistrantCity' => 'Budapest',
                    'RegistrantStateProvince' => 'Budapest',
                    'RegistrantPostalCode' => '1084',
                    'RegistrantCountry' => 'Hungary',
                    'RegistrantPhone' => '+36.40242242',
                    'RegistrantEmailAddress' => 'me@iamattila.com',
                    'TechFirstName' => 'Attila',
                    'TechLastName' => 'Odri',
                    'TechAddress1' => 'Jozsef korut 34',
                    'TechCity' => 'Budapest',
                    'TechStateProvince' => 'Budapest',
                    'TechPostalCode' => '1084',
                    'TechCountry' => 'Hungary',
                    'TechPhone' => '+36.40242242',
                    'TechEmailAddress' => 'me@iamattila.com',
                    'AdminFirstName' => 'Attila',
                    'AdminLastName' => 'Odri',
                    'AdminAddress1' => '+Jozsef korut 34',
                    'AdminCity' => 'Budapest',
                    'AdminStateProvince' => 'Budapest',
                    'AdminPostalCode' => '1084',
                    'AdminCountry' => 'Hungary',
                    'AdminPhone' => '+36.40242242',
                    'AdminEmailAddress' => 'me@iamattila.com',
                    'AuxBillingFirstName' => 'Attila',
                    'AuxBillingLastName' => 'Odri',
                    'AuxBillingAddress1' => 'Jozsef korut 34',
                    'AuxBillingCity' => 'Budapest',
                    'AuxBillingStateProvince' => 'Budapest',
                    'AuxBillingPostalCode' => '1084',
                    'AuxBillingCountry' => 'Hungary',
                    'AuxBillingPhone' => '+36.40242242',
                    'AuxBillingEmailAddress' => 'me@iamattila.com',
                ],
            ]
        ];

        foreach ($entries as $entry) {
            Settings::create($entry);
        }
    }
}
