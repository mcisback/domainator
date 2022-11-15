<?php

use App\Models\FtpServer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


Route::get('/', function () {
    return Inertia('Dashboard/Whois');
})->name('index');


Route::resource('me', \App\Http\Controllers\MeController::class)->only(
    'index' , 'update'
);

Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only([
    'index', 'update', 'store', 'destroy',
]);

Route::resource('sedoAccounts', \App\Http\Controllers\SedoAccountController::class)->only([
    'index', 'update', 'store', 'destroy',
]);

Route::resource('domains', \App\Http\Controllers\DomainController::class)->only([
    'index', 'update', 'store', 'destroy',
]);

Route::get('/roles-and-permissions', function () {
    $records = [];

    $records = User::all()->map(function ($user) use($records) {
        return [
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'username' => $user->username,
            'email' => $user->email,
            'roles' => $user->roles,
            'permissions' => $user->getAllPermissions()->map(function ($permission) {
                return $permission->only('name')['name'];
            })->toArray()
        ];
    })->toArray();

    return Inertia('Admin/RolesAndPermissions', [
        'users' => $records,
        'permissions' => Permission::all()->map(function ($permission) {
            return $permission->only('id', 'name');
        }),
        'roles' => Role::all()->map(function ($role) {
            return $role->only('id', 'name');
        })
    ]);
})->name('roles-and-permissions');
