<?php

use App\Http\Controllers\ClassController;
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

Route::controller(ClassController::class)
    ->group(function () {
        Route::inertia('/', 'Class/list')->name('class_list_view');
        Route::inertia('/details', 'Class/details')->name('class_details_view');
    });
