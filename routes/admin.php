<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

//Route::group(['prefix' => '/admin', 'as' => 'admin.', 'middleware' => ['auth:web', 'role:admin|staff']], function () {
//    Route::get('/', function() {
//        return Inertia('Dashboard/Admin/Index')->name('index');
//    });
//});

