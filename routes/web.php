<?php

use Illuminate\Support\Facades\Route;
use Tightenco\Ziggy\Ziggy;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return Inertia('Home');
})->name('home');

Auth::routes();

Route::get('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);

Route::group(['prefix' => '/api', 'as' => 'api.'], function () {

    require __DIR__ . '/api.php';

});

Route::group(['prefix' => '/dashboard', 'as' => 'dashboard.', 'middleware' => ['auth:web', 'role:admin|staff']], function () {

    require_once __DIR__ . '/admin.php';
    require_once  __DIR__.'/dashboard.php';


    Route::post('/generator', [\App\Http\Controllers\GeneratorController::class, 'store'])->name('generator');

});


