<?php

use App\Http\Controllers\UserController;
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

Route::controller(UserController::class)
    ->group(function () {
        Route::get('/', 'api_getall')->name('api_getall');
        Route::get('/student', 'api_getStudent')->name('api_getStudent');
        Route::get('/teacher', 'api_getTeacher')->name('api_getTeacher');
        Route::get('/officer', 'api_getAdmin')->name('api_getAdmin');

        Route::post('/create', 'api_create')->name('api_create');
        Route::put('/update/{id}', 'api_update')->name('api_update');

        // Route::delete('/delete', 'api_delete')->name('api_delete');
        Route::delete('/delete/{id}', 'api_delete')->name('api_delete');
    });
