<?php

use App\Http\Controllers\HomeworkController;
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

Route::controller(HomeworkController::class)
    ->group(function () {
        Route::inertia('/', 'Homework/list')->name('homework_list_view');
        Route::inertia('/classroom', 'Homework/classroom')->name('homework_classroom_view');
        Route::inertia('/classroom/task/', 'Homework/task')->name('homework_task_view');
    });
