<?php

namespace App\Http\Controllers\Api;

use App\Api\Namecheap\NamecheapApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

//        return response()->json([
//            'success' => false,
//            'message' => 'Debug ' . $domain,
//            'isAvailable' => false,
//            'domain' => $domain,
//        ], 500);

        if(\App\Models\Domain::where('domain', $domain)->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Domain has already been requested for registration',
                'isAvailable' => false,
                'domain' => $domain,
            ]);
        }

        $namecheapApi = new NamecheapApi();

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
}
