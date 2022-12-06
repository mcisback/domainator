<?php

namespace App\Api\Namecheap;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class NamecheapApi
{
    public function __construct($username='', $apiKey='') {
        $this->isSandbox = Config::get('isSandbox', true);
        $this->clientIp = Config::get('clientIp', '127.0.0.1');
        $this->isSuccessful = false;
        $this->xml = null;
        $this->tokenId = null;

        $credentials = Cache::remember(
            'namecheap',
            1800, // Time To Live
            function() {
                return Settings::get('namecheap');
        });

        $this->apiUser = $credentials['ApiUser'];
        $this->apiKey = $credentials['ApiKey'];

        if($this->isSandbox === true) {
            $this->endpoint = "https://api.sandbox.namecheap.com/xml.response";
        } else  {
            $this->endpoint = "https://api.namecheap.com/xml.response";
        }
    }

    public function buildUrl($command, array $params) {

        return $this->endpoint . "?" . http_build_query([
            'ApiUser' => $this->apiUser,
            'ApiKey' => $this->apiKey,
            'UserName' => $this->apiUser,
            'ClientIp' => $this->clientIp,
            'Command' => "namecheap.$command",
            ...$params
        ]);
    }

    public function run($command, array $params=[]) {
        $res = Http::get($this->buildUrl($command, $params));

        if($res->successful()) {
            $this->xml = json_decode(
                json_encode(
                    simplexml_load_string($res->body())
                ),
                true
            );

            $this->isSuccessful = $this->xml["@attributes"]["Status"] === "OK";

            if(!$this->isSuccessful) {
                $errorString = join("\n", $this->xml["Errors"]);

                throw new \Exception("Namecheap Error: " . $errorString);
            }

            return $this;
        } else {
            throw new \Exception("Namecheap HTTP Error: " . $res->status() . ": \n" . $res->body());
        }
    }

    public function successful() {
        return $this->isSuccessful;
    }

    public function commandResponse() {
        return $this->xml['CommandResponse'];
    }

    public function json() {
        return $this->xml;
    }

    public function domainsCheckAvailability($domains) {
        $isArray = false;

        if(is_array($domains)) {
            $isArray = true;
            $domains = join(',', $domains);
        }

        $this->run('domains.check', [
            'DomainList' => $domains
        ]);

        $xml = $this->commandResponse();

        if($isArray) {
            $result = [];

            foreach($xml["DomainCheckResult"] as $domainResult) {
                $result[
                    $domainResult["@attributes"]["Domain"]
                ] = $domainResult["@attributes"]["Available"] === 'true';
            }

            return $result;
        }

        return $xml["DomainCheckResult"]["@attributes"]["Available"] === "true";
    }

    public function getBalances() {
        $this->run('users.getBalances');

        $xml = $this->commandResponse()["UserGetBalancesResult"]["@attributes"];

        return [
            'currency' => $xml["Currency"],
            'available' => $xml["AvailableBalance"],
            'accountBalance' => $xml["AccountBalance"],
        ];
    }

    public function hasBalance() {
        $balance = $this->getBalances();

        return floatval($balance['available']) > 0;
    }

    public  function getDomainPricings(string $domain) {
        $tld = explode('.', $domain)[1];

        return $this->getTldPricings($tld);
    }

    public function getTldPricings(string $tld) {
        $this->run('users.getPricing', [
            'ProductType' => 'DOMAIN',
            'ProductCategory' => 'DOMAINS',
            'ActionName' => 'REGISTER',
            'ProductName' => strtoupper($tld),
        ]);

        $pricings = $this->commandResponse()["UserGetPricingResult"]["ProductType"]["ProductCategory"]["Product"];

        $result = [];

        foreach ($pricings["Price"] as $pricing) {
            $attr = $pricing["@attributes"];

            $result[ $attr["Duration"] . " " . $attr["DurationType"] ] = [
                "regular" => $attr["RegularPrice"],
                "yourPrice" => $attr["YourPrice"],
                "promotion" => $attr["PromotionPrice"],
                "currency" => $attr["Currency"],
            ];
        }

        return $result;
    }

    public function addFundsRequest($amount, $returnUrl = '') {
        $this->run('users.createaddfundsrequest', [
            'PaymentType' => 'Creditcard',
            'Amount' => $amount,
            'ReturnUrl' => $returnUrl,
        ]);

        $response = $this->commandResponse()["Createaddfundsrequestresult"]["@attributes"];

        return [
            'tokenId' => $response["TokenID"],
            'returnUrl' => $response["ReturnURL"],
            'redirectUrl' => $response["RedirectURL"],
        ];
    }

    public function registerDomain(string $domain, array $domainData) {
        $this->run('domains.create', [
            'DomainName' => $domain,
            'Years' => '1',
            // 'PromotionCode' => 'COUPONCODE',
            // 'AddFreeWhoisguard' => 'yes', // For Free Whois
            // 'WGEnabled' => 'yes',
            ...$domainData,
        ]);

        return $this->commandResponse()["DomainCreateResult"]["@attributes"];
    }

    public function addDNSRecordsToDomain($domain, array $data) {
        $domain = new \Utopia\Domains\Domain($domain);

        $dnsRecords = [];
        $count = 1;

        foreach ($data as $dnsRecord) {
            foreach ($dnsRecord as $key => $value) {
                $dnsRecords["$key$count"] = $value;
            }

            $count++;
        }

        $this->run('domains.dns.setHosts', [
            'SLD' => $domain->getName(),
            'TLD' => $domain->getSuffix(),

            ...$dnsRecords,
        ]);

        return $this->commandResponse()["DomainDNSSetHostsResult"]["@attributes"];
    }
}
