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
        Route::inertia('/student', 'Users/student_list')->name('users_student_list_view');
        Route::inertia('/teacher', 'Users/teacher_list')->name('users_teacher_list_view');
        Route::inertia('/officer', 'Users/officer_list')->name('users_officer_list_view');
    });
