<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tightenco\Ziggy\Ziggy;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/ziggy', fn () => response()->json(new Ziggy));

Route::any('/', [\App\Http\Controllers\Api\ApiController::class, 'index'])->name('index');

/*Route::group(['prefix' => '/api', 'as' => 'api.'], function () {

    Route::get('', fn() => response()->json([]))->name('index');

// For everybody
    // Route::get('/ziggy', fn () => response()->json(new Ziggy))->name('ziggy');

// For users
    Route::group(['prefix' => '/members', 'as' => 'members.', 'middleware' => ['auth:web', 'role:superadmin|staff|member']], function () {

        Route::get('/permissions', fn () => response()->json(Permission::all()))->name('permissions');

        Route::get('/roles', fn () => response()->json(Role::all()))->name('roles');

    });

// For admins
    Route::group(['prefix' => '/admin', 'as' => 'admin.', 'middleware' => ['auth:web', 'role:superadmin|staff']], function () {

        Route::resource('/permissions', \App\Http\Controllers\PermissionController::class)
//        ->name('permissions')
        ;

        Route::resource('/users', \App\Http\Controllers\Admin\UserController::class);

        Route::post('/users/togglepermission/{userId}', [\App\Http\Controllers\Admin\UserController::class, 'togglePermission'])
            ->name('users.togglepermission')
        ;


    });

});*/
