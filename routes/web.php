<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FileController;
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route::get('/me', function (Request $request) {
    //     return $request->user();
    // });
    Route::inertia('/', 'index')->name('index');
    Route::prefix('absent')->group(base_path('routes/frontend/absent.php'));
    Route::prefix('coming-history')->group(base_path('routes/frontend/coming-history.php'));
    Route::prefix('classroom')->group(base_path('routes/frontend/classroom.php'));
    Route::prefix('timetable')->group(base_path('routes/frontend/timetable.php'));
    Route::prefix('grade')->group(base_path('routes/frontend/dooGrade.php'));
    Route::prefix('missing-items')->group(base_path('routes/frontend/missing-items.php'));
    Route::prefix('users')->group(base_path('routes/frontend/users.php'));
    Route::prefix('class')->group(base_path('routes/frontend/class.php'));


    Route::get('/download/{fileName}', [FileController::class, 'getFile']);
});
