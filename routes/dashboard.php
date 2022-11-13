<?php

use App\Models\FtpServer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


Route::get('/', function () {

    $user = Auth::user();

    $servers = $user->isAdmin()
        ?
        FtpServer::orderBy('created_at', 'desc')->get()
        :
        FtpServer::orderBy('created_at', 'desc')->where('user_id', $user->id)->get();

    return Inertia('Dashboard/Generator', [
        'servers' => $servers,
    ]);
})->name('index');

//Route::post('generator/publishToFtp', [\App\Http\Controllers\GeneratorController::class, 'publishToFtp'])
//    ->name('generator.publishToFtp');

Route::resource('translations', \App\Http\Controllers\TranslationsController::class)->only([
    'index', 'update'
])->parameters([
    'translations' => 'template'
]);

Route::resource('ftpserver', \App\Http\Controllers\FtpServerController::class)->only(
    'index', 'store', 'destroy'
);

Route::post('ftpserver/list', [\App\Http\Controllers\FtpServerController::class, 'listDirectories'])
    ->name('ftpserver.list');

Route::post('ftpserver/test', [\App\Http\Controllers\FtpServerController::class, 'testConnection'])
    ->name('ftpserver.test');

Route::post('ftpserver/publish', [\App\Http\Controllers\FtpServerController::class, 'publishToFtp'])
    ->name('ftpserver.publish');

Route::post('ftpserver/start', [\App\Http\Controllers\FtpServerController::class, 'startPublishToFtp'])
    ->name('ftpserver.start');

Route::resource('me', \App\Http\Controllers\MeController::class)->only(
    'index' , 'update'
);

Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only([
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

Route::resource('editor', \App\Http\Controllers\EditorController::class)->only([
    'index', 'update', 'store', 'destroy',
])->parameters([
    'editor' => 'template'
]);;
