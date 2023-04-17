<?php

namespace App\Http\Controllers;

use App\Api\Sedo\SedoApi;
use App\Models\Domain;
use App\Models\DomainRegistrationRequest;
use App\Models\SedoAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DomainRegistrationRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Dashboard/Admin/DomainsRequests', [
            'domainRequests' => DomainRegistrationRequest::all(),
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
/*    public function create()
    {
        //
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $domainRegistrationRequest = \App\Models\DomainRegistrationRequest::create([
                'submitted_by_user_id' => Auth::id(),
                'submitted_at' => Carbon::now(),
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Domain Registration Request Created Successful',
            'domainRegistrationRequestId' => $domainRegistrationRequest->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DomainRegistrationRequest  $domainRegistrationRequest
     * @return \Illuminate\Http\Response
     */
/*    public function show(DomainRegistrationRequest $domainRegistrationRequest)
    {
        //
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DomainRegistrationRequest  $domainRegistrationRequest
     * @return \Illuminate\Http\Response
     */
/*    public function edit(DomainRegistrationRequest $domainRegistrationRequest)
    {
        //
    }*/

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DomainRegistrationRequest  $domainRegistrationRequest
     * @return \Illuminate\Http\Response
     */
/*    public function update(Request $request, DomainRegistrationRequest $domainRegistrationRequest)
    {
        //
    }*/

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DomainRegistrationRequest  $domainRegistrationRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DomainRegistrationRequest $domainRegistrationRequest)
    {
        try {
            $domainRegistrationRequest->deleteOrFail();
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Domain Registration Request Deleted Successful',
            'domainRequests' => DomainRegistrationRequest::all(),
        ]);
    }
}
