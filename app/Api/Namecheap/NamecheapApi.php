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
                return Settings::getValue('namecheap');
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
            $this->xml = \simplexml_load_string( $res->body() );
            $this->rawXml = $res->body();

            $this->isSuccessful = ( (string) $this->xml->attributes()->Status ) === "OK";

            if(!$this->isSuccessful) {
                $errorString = join("\n", (array) $this->xml->Errors);

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
        return $this->xml->CommandResponse;
    }

//    public function json() {
//        return $this->xml;
//    }

    public function domainsCheckAvailability(array|string $domains) {
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

            foreach($xml->DomainCheckResult as $domainResult) {
                $attrs = $domainResult->attributes();
                $domain = new \Utopia\Domains\Domain( (string) $attrs->Domain );
                $isPremium = ( (string) $attrs->IsPremiumName ) === 'true';

                $result[ $domain->getRegisterable() ] = [
                    'available' => ( (string) $attrs->Available) === 'true',
                    'isPremium' =>$isPremium,
                    'price' => $isPremium
                        ?
                        (string) $attrs->PremiumRegistrationPrice
                        :
                             $this->getTDLPricing( $domain->getSuffix() )[1]
                ];
            }

            return $result;
        }

        $attrs = $xml->DomainCheckResult->attributes();
        $domain = new \Utopia\Domains\Domain( (string) $attrs->Domain );
        $isPremium = ( (string) $attrs->IsPremiumName ) === 'true';

        return [
            'available' => ( (string) $attrs->Available) === 'true',
            'isPremium' =>$isPremium,
            'price' => $isPremium
                ?
                    (string) $attrs->PremiumRegistrationPrice
                :
                $this->getTDLPricing( $domain->getSuffix() )[1]
//                    0
        ];
    }

    public function getBalances() {
        $this->run('users.getBalances');

        $xml = $this->commandResponse()->UserGetBalancesResult->attributes();

        return [
            'currency' => (string) $xml->Currency,
            'available' => (string) $xml->AvailableBalance,
            'accountBalance' => (string) $xml->AccountBalance,
        ];
    }

    public function hasBalance() {
        $balance = $this->getBalances();

        return floatval($balance['available']) > 0;
    }

    public  function getDomainPricings(string $domain) {
        $tld = explode('.', $domain)[1];

        return $this->getTDLPricing($tld);
    }

    public function addFundsRequest($amount, $returnUrl = '') {
        $this->run('users.createaddfundsrequest', [
            'PaymentType' => 'Creditcard',
            'Amount' => $amount,
            'ReturnUrl' => $returnUrl,
        ]);

        $response = $this->commandResponse()->Createaddfundsrequestresult->attributes();

        return [
            'tokenId' => $response->TokenID,
            'returnUrl' => $response->ReturnURL,
            'redirectUrl' => $response->RedirectURL,
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

        return $this->commandResponse()->DomainCreateResult->attributes();
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

        return $this->commandResponse()->DomainDNSSetHostsResult->attributes();
    }

    public function getTDLPricing(string $tdl) {
        $this->run('users.getPricing', [
            'ProductType' => 'DOMAIN',
            'ProductCategory' => 'DOMAINS',
            'ActionName' => 'REGISTER',
            'ProductName' => strtoupper( $tdl ),
        ]);

        $productType = $this->commandResponse()->UserGetPricingResult->ProductType;

        if (!property_exists($productType, "ProductCategory")) {
            throw new \Exception("Namecheap Error: TDL $tdl not supported");
        }

        $pricing = [];

        foreach ($productType->ProductCategory->Product->Price as $tdlData) {
            $attrs = $tdlData->attributes();

            $price = floatval( (string) $attrs->Price );
            $price += property_exists($attrs, "RegularAdditionalCost") ? floatval( (string) $attrs->RegularAdditionalCost) : 0;

            $pricing[ (string) $attrs->Duration ] = $price;
        }

        return $pricing;
    }
}
