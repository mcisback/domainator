<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GotCreations\Namecheap\Facade\Namecheap;
use Illuminate\Http\Request;
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

        // Creating default configured client
        $whois = \Iodev\Whois\Factory::get()->createWhois();

        // Checking availability
        if ($whois->isDomainAvailable($domain)) {
//        if( Namecheap::check($domain) ) {
            return response()->json([
                'success' => true,
                'message' => 'Domain is available',
                'isAvailable' => true,
                'domain' => $domain,
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
        $domain = $request->get('domain');

        try {
            \App\Models\Domain::create([
                'domain' => $domain,
                'requested_by_user_id' => Auth::id(),
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'requested' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'requested' => true,
            'message' => 'Domain Registration Request Successful',
            'domain' => $domain,
            'isAvailable' => false
        ]);
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
