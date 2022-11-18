<?php

namespace App\Api\Namecheap;

class NamecheapApi
{
    public function __construct() {
        $this->isSandbox = env('NC_API_SANDBOX', true);
        $this->clientIp = env('NC_API_CLIENT_IP', '127.0.0.1');

        if($this->isSandbox === true) {
            $this->endpoint = "https://api.sandbox.namecheap.com/xml.response";
        } else  {
            $this->endpoint = "https://api.namecheap.com/xml.response";
        }
    }
}
