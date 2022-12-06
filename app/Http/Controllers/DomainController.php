<?php

namespace App\Http\Controllers;

use App\Api\Namecheap\NamecheapApi;
use App\Api\Sedo\SedoApi;
use App\Models\Domain;
use App\Models\SedoAccount;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DomainController extends Controller
{
    public function __construct() {
        $this->middleware([
            'auth:web',
            'permission:domains.manage'
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Dashboard/Admin/DomainsRequests', [
            'domains' => Domain::all(),
            'sedoAccounts' => SedoAccount::all(),
            'sedoCategories' => SedoApi::getDomainCategories(),
            'sedoLanguages' => SedoApi::getDomainLanguages(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $domain = $request->get('domain');

        try {
            \App\Models\Domain::create([
                'domain' => $domain,
                'submitted_by_user_id' => Auth::id(),
                'submitted_at' => Carbon::now(),
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function edit(Domain $domain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Domain $domain)
    {
        //
    }

    /**
     * Register the domain the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function register(Request $request, Domain $domain)
    {
        $enableWhoisProtection = $request->get('enableWhoisProtection');
        $namecheapApi = new NamecheapApi();

        try {
            $domainData = Settings::get('namecheap');

            if($enableWhoisProtection === true) {
                $domainData['AddFreeWhoisguard'] = 'yes';
                $domainData['WGEnabled'] = 'yes';
            }

            unset($domainData['ApiUser']);
            unset($domainData['ApiKey']);

            $response = $namecheapApi->registerDomain($domain->domain, $domainData);

            if($response["Registered"] === true || $response["Registered"] === "true") {
                $response = "Domain {$response["Domain"]} successfully registered for {$response["ChargedAmount"]}";

                $nowTimestamp = Carbon::now();

                $domain->approved_by_user_id = Auth::id();
                $domain->approved_at = $nowTimestamp;

                $domain->registered = true;
                $domain->registered_at = $nowTimestamp;

                $domain->save();
            } else {
                $response = "Error registering {$response["Domain"]}: " . json_encode($response);
            }

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
//            'message' => 'Namecheap Domain Registration Successful',
            "message" => $response,
            'domains' => Domain::all(),
        ]);
    }

    /**
     * Register the domain the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function addToSedo(Request $request, Domain $domain, SedoAccount  $sedoAccount)
    {
        $sedoCategoryIds = $request->get('sedoCategoryIds');
        $isForSale = $request->get('isForSale') === true ? 1 : 0;
        $price = $request->get('price') ?? 0;
        $minprice = $request->get('minprice') ?? 0;
        $fixedprice = $request->get('fixedprice') === true ? 1 : 0;
//        $currencyId = $request->get('currencyId') ?? 1;
        $sedoDomainLanguageId = $request->get('sedoDomainLanguageId') ?? 'en';

//        print_r([
//            'domain'         => $domain->domain,
//            'category'       => $sedoCategoryIds,
//            'forsale'        => $isForSale,
//            'price'          => $price,
//            'minprice'       => $minprice,
//            'fixedprice'     => $fixedprice,
//            'currency'       => 1, // USD
//            'domainlanguage' => $sedoDomainLanguageId,
//        ]);

        try {
            $sedoApi = new SedoApi($sedoAccount);

            $response = $sedoApi->domainInsert([
                [
                    'domain'         => $domain->domain,
                    'category'       => $sedoCategoryIds,
                    'forsale'        => $isForSale,
                    'price'          => $price,
                    'minprice'       => $minprice,
                    'fixedprice'     => $fixedprice,
                    'currency'       => 1, // USD
                    'domainlanguage' => $sedoDomainLanguageId,
                ],
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "SEDO Error: " . $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }

        $domain->sedo_account_id = $sedoAccount->id;
        $domain->save();

        return response()->json([
            'success' => true,
            'message' => 'Domains Added To SEDO Successful',
            'domains' => Domain::all(),
            'sedoDomains' => $response,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Domain $domain)
    {
        try {
            $domain->deleteOrFail();
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
            'message' => 'Domain Deleted',
            'domains' => Domain::all(),
        ]);
    }
}
