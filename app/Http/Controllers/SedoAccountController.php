<?php

namespace App\Http\Controllers;

use App\Models\SedoAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SedoAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Dashboard/Admin/SedoAccounts', [
            'sedoAccounts' => SedoAccount::all()
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

//            \App\Models\SedoAccount::create([
//                'name' => $request->get('name'),
//                'username' => $request->get('username'),
//                'password' => $request->get('password'),
//                'partner_id' => $request->get('partner_id'),
//                'signkey' => $request->get('signkey'),
//            ]);

            \App\Models\SedoAccount::create( $request->all() );

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
            'message' => 'SEDO Account Added Successfully',
            'sedoAccounts' => SedoAccount::all(),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SedoAccount  $sedoAccount
     * @return \Illuminate\Http\Response
     */
    public function show(SedoAccount $sedoAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SedoAccount  $sedoAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(SedoAccount $sedoAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SedoAccount  $sedoAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SedoAccount $sedoAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SedoAccount  $sedoAccount
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(SedoAccount $sedoAccount)
    {
        try {

            $sedoAccount->deleteOrFail();

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
            'message' => 'SEDO Account Deleted Successfully',
            'sedoAccounts' => SedoAccount::all(),
        ]);
    }
}
