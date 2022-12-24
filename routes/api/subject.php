<?php

use App\Http\Controllers\SubjectController;
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

Route::controller(SubjectController::class)
    ->group(function () {
        Route::get('/', 'api_getSubject')->name('api_getSubject');
        Route::get('/teacher/{teacher}', 'api_getSubjectByTeacher')->name('api_getSubjectByTeacher');


        Route::post('/create', 'api_create')->name('api_create');

        Route::put('/update/{id}', 'api_update')->name('api_update');

        Route::delete('/delete/{id}', 'api_delete')->name('api_delete');
    });
