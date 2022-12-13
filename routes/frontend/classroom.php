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
        Route::inertia('/', 'Classroom/list')->name('classroom_list_view');
        Route::inertia('/room', 'Classroom/room')->name('classroom_room_view');
        Route::inertia('/room/task/', 'Classroom/task')->name('classroom_task_view');

        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::inertia('/edit', 'Classroom/edit')->name('classroom_edit_view');
                Route::inertia('/edit-annouce', 'Classroom/edit_annouce')->name('classroom_edit_annouce_view');
            });
    });
