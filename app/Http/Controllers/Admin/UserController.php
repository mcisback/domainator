<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
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
        return Inertia('Dashboard/Admin/Users', [
            'users' => User::all()->makeVisible(['password']),
            'columns' => \App\Models\User::getColumnsNamesTypeMapStatic(),
            'roles' => User::getAllRoles(),
        ]);
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
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            $user->assignRole(Role::find($data['roles']));
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'User Created',
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id=null)
    {
        try {
            $user = User::find($id);
            $password = $request->get('password');
            $role = Role::find($request->get('roles'));

            if($user->password !== $password) {
                $user->password = Hash::make($password);
                $user->save();
            }

            if(!$user->hasRole($role->name)) {
                $user->syncRoles($role);
            }

            $user->update($request->except('password', 'roles'));

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'User Updated',
            'user' => $user,
            'records' => User::all()->makeVisible(['password']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function togglePermission(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json(['success' => false, 'err' => 'User not found']);
        }

        $permission = $request->get('permission');
        $value = $request->get('value');

        if($value === true) {
            if(!$user->hasPermissionTo($permission)) {
                $user->givePermissionTo($permission);
            }
        } else {
            if($user->hasPermissionTo($permission)) {
                $user->revokePermissionTo($permission);
            }
        }

        return response()->json(['success' => true, 'data' => [
            'user' => $user,
            'postedData' => $request->all(),
        ]]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $ids = explode(',', $id);

            foreach ($ids as $id) {
                $user = User::find($id);
                $user->delete();
            }

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'User Deleted',
            'deleted' => $ids,
            'records' => User::all()->makeVisible(['password']),
        ]);
    }
}
