<?php

namespace App\Api\Sedo;

use App\Models\SedoAccount;
use Illuminate\Support\Facades\Http;

define('SEDO_ENDPOINT', 'https://api.sedo.com/api/v1/');

class SedoApi
{
    public function __construct($sedoAccount) {
        if(is_numeric($sedoAccount) || is_string($sedoAccount)) {
            $this->sedoAccount = SedoAccount::findOrFail($sedoAccount);
        } else {
            if(is_object($sedoAccount) && $sedoAccount instanceof SedoAccount) {
                $this->sedoAccount = $sedoAccount;
            } else {
                throw new \Exception("Wrong SedoApi sedoAccount, not an object or valid id");
            }
        }

        $this->sedoClient = new \SoapClient(
            null, // WSDL ?
            [
                'location' => SEDO_ENDPOINT,
                'soap_version' => SOAP_1_1,
                'encoding' => 'UTF-8',
                'uri' => 'urn:SedoInterface',
                'style' => SOAP_RPC,
                'use' => SOAP_ENCODED
            ]
        );
    }

    public static function getDomainCategories() {
        $xml = static::doGetRequest('DomainCategories');

        $mainCategories = $xml->maincategory;

        $categories = [];

        foreach ($mainCategories as $mainCategory) {
            $attr = $mainCategory->attributes();
            $cat = "" . $attr->name;

            $id = $attr->id;
            $categories["$id"] = $cat;

            foreach ($mainCategory->subcategory1 as $subCategory1) {
                $cat = $attr->name . " - " . $subCategory1->attributes()->name;

                $id = $subCategory1->attributes()->id;
                $categories["$id"] = $cat;

                foreach ($subCategory1->subCategory2 as $subCategory2) {
                    $cat =  $attr->name . " - " . $subCategory1->attributes()->name . " - " . $subCategory2->attributes()->name;

                    $id = $subCategory2->attributes()->id;
                    $categories["$id"] = $cat;
                }
            }
        }

        return $categories;
    }

    public static function getDomainLanguages() {
        $xml = static::doGetRequest('DomainLanguages');

        $languages = [];

        foreach ($xml->language as $xmlLanguage) {
            $iso = (string) $xmlLanguage->attributes()->iso;

            $languages[$iso] = (string) $xmlLanguage;
        }

        return $languages;
    }

    public function getCurrencies() {
        return [
            'EUR',
            'USD',
            'GBP',
        ];
    }

    public function getCredentials() {
        return [
            'partnerid'   => $this->sedoAccount->partner_id,
            'signkey'     => $this->sedoAccount->signkey,
            'username'    => $this->sedoAccount->username,
            'password'    => $this->sedoAccount->password,
        ];
    }

    public function domainInsert(array $domains) {
        // Set the values for the array
        $params = [

            ...$this->getCredentials(),

            'domainentry' => $domains,
        ];

        return $this->sedoClient->DomainInsert($params);
    }

    public static function doGetRequest($action, $params=[]) {
        $params = [
            'output_method' => 'xml',
            'language'      => 'en',
            ...$params
        ];

        $requestUrl = SEDO_ENDPOINT . "/$action?" . http_build_query($params);

        $res = Http::get( $requestUrl );

        if($res->successful()) {
            $xml = simplexml_load_string($res->body());

            $isSuccessful = count($xml->faultstring) > 0 ? false : true;

            if(!$isSuccessful) {
                $errorString = join("\n", $xml->faultstring);

                throw new \Exception("SEDO API Error: " . $errorString);
            }

            return $xml;
        } else {
            throw new \Exception("SEDO API HTTP Error: " . $res->status() . ": \n" . $res->body());
        }
    }
}
