<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MeController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:web', 'role:admin|staff']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Dashboard/Profile', [
            'currentUser' => [
                ...Auth::user()->toArray(),
                'password' => Auth::user()->password,
            ],
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

//    /**
//     * Display the specified resource.
//     *
//     * @param  int  $id
//     * @return \Inertia\Response
//     */
//    public function show($id)
//    {
//        if(Auth::user()->id == $id) {
//            return Inertia::render('Dashboard/Profile', [
//                'currentUser' => [
//                    ...Auth::user()->toArray(),
//                    'password' => Auth::user()->password,
//                ],
//            ]);
//        } else {
//            return Inertia::render('Error/Error', [
//                'status' => 403
//            ]);
//        }
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->id == $id) {
            $user = User::find($id);
            $user->update($request->all());

            return Inertia::render('Dashboard/Profile', [
                'status' => 'Profilo Aggiornato Correttamente'
            ]);
        } else {
            return Inertia::render('Error/Error', [
                'status' => 403
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
