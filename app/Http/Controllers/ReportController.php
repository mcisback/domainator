<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:web', 'role:admin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response|\Inertia\ResponseFactory
     */
    public function index()
    {
        return Inertia('Dashboard/Admin/Reports', [
            'users' => User::all()->makeVisible(['password']),
            'columns' => \App\Models\User::getColumnsNamesTypeMapStatic(),
            'roles' => User::getAllRoles(),
        ]);
    }
}
