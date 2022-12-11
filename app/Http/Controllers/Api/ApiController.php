<?php

namespace App\Http\Controllers\Api;

use App\Api\Namecheap\NamecheapApi;
use App\Facades\Namecheap;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web');
    }

    public function index(Request $request) {
        $action = $request->get('action');

        if(method_exists($this, $action)) {
            return $this->$action($request);
        }

        return response()->json([
            'success' => false,
            'error' => 'Action not allowed',
        ], 403);
    }

    public function checkDomain(Request $request) {
        $domain = $request->get('domain');

        if(\App\Models\Domain::where("domain", $domain)->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Domain has already been requested for registration',
                'isAvailable' => false,
                'domain' => $domain,
            ]);
        }

//        $domain = new \Utopia\Domains\Domain($domain);
//        $tdl = $domain->getSuffix();
//
        $namecheapApi = new NamecheapApi();
//
//        try {
//            $pricing = $namecheapApi->getTDLPricing($tdl)[1];
//        } catch(\Exception $e) {
//            return response()->json([
//                'success' => false,
//                'message' => $e->getMessage(),
//                'line' => $e->getLine(),
//                'isAvailable' => false,
//                'domain' => $domain,
//                'tdl' => $tdl,
//            ]);
//        }

        // Creating default configured client
//        $whois = \Iodev\Whois\Factory::get()->createWhois();

        // Checking availability
//        if ($whois->isDomainAvailable($domain->getRegisterable())) {
        $isAvailable = $namecheapApi->domainsCheckAvailability($domain);
        if( $isAvailable["available"] ) {
            return response()->json([
                'success' => true,
                'message' => 'Domain is available',
                'isAvailable' => true,
                'domain' => $domain,
                'price' => $isAvailable["price"],
                'isPremium' => $isAvailable["isPremium"],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Domain is already registered',
            'isAvailable' => false,
            'domain' => $domain,
        ]);
    }

    public function requestDomainRegistration(Request $request) {

    }

//    public function getTemplates(Request $request) {
//        $getContents = $request->get('getContents') ?? false;
//
//        return response()->json([
//            'success' => true,
//            'templates' => TranslationsHelper::getTemplates($getContents),
//        ]);
//    }
}
