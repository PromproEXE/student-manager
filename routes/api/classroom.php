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
        Route::get('/', 'api_getClassroom')->name('api_getClassroom');
        Route::get('/users/{id}', 'api_getClassroomByUserId')->name('api_getClassroomByUserId');
        Route::get('/{id}', 'api_getClassroomById')->name('api_getClassroomById');
        Route::get('/{id}/post/{post_id}', 'api_getPost')->name('api_getPost');
        Route::get('/{id}/teacher/name', 'api_getTeacherName')->name('api_getTeacherName');
        Route::get('/teacher/{teacher}', 'api_getClassroomByTeacher')->name('api_getClassroomByTeacher');
        Route::get('/room/amount', 'api_getRoomAmount')->name('api_getRoomAmount');


        Route::post('/auto-create', 'api_autoCreate')->name('api_autoCreate');
        Route::post('/{subject_id}/create/post', 'api_createPost')->name('api_createPost');

        Route::put('/update/{id}', 'api_update')->name('api_update');
        Route::put('/{id}/post/{post_id}/update', 'api_updatePost')->name('api_updatePost');

        Route::delete('/delete/{id}', 'api_delete')->name('api_delete');
        Route::delete('/{id}/post/{post_id}/delete', 'api_deletePost')->name('api_deletePost');
    });
