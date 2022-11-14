<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
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

        // Creating default configured client
        $whois = \Iodev\Whois\Factory::get()->createWhois();

        // Checking availability
        if ($whois->isDomainAvailable($domain)) {
            return response()->json([
                'success' => true,
                'isAvailable' => true,
                'domain' => $domain,
            ]);
        }

        return response()->json([
            'success' => true,
            'isAvailable' => false,
            'domain' => $domain,
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
