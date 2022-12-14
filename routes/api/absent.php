<?php

use App\Http\Controllers\AbsentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::controller(AbsentController::class)
    ->group(function () {
        Route::get('/', 'api_getall');
        Route::get('/id/{id}', 'api_getFromId');
        Route::get('/user_id/{user_id}', 'api_getFromUserId');
        Route::post('/create', 'api_create');
    });
