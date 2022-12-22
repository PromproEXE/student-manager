<?php

use App\Http\Controllers\MissingItemController;
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

Route::controller(MissingItemController::class)
    ->group(function () {
        Route::inertia('/', 'Missing-items/list')->name('missing_items_list_view');
        Route::inertia('/edit', 'Missing-items/edit')->name('missing_items_edit_view');
    });
