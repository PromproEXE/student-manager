<?php

use App\Http\Controllers\ClassroomController;
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

Route::controller(ClassroomController::class)
    ->group(function () {
        Route::get('/room-amount', 'api_getRoomAmount')->name('api_getRoomAmount');
    });
