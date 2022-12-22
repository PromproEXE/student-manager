<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::prefix('absent')->group(base_path('routes/api/absent.php'));
    Route::prefix('grade')->group(base_path('routes/api/grade.php'));
    Route::prefix('file')->group(base_path('routes/api/file.php'));
    Route::prefix('classroom')->group(base_path('routes/api/classroom.php'));
    Route::prefix('subject')->group(base_path('routes/api/subject.php'));
    Route::prefix('users')->group(base_path('routes/api/users.php'));
    Route::prefix('timetable')->group(base_path('routes/api/timetable.php'));
    Route::prefix('missing-items')->group(base_path('routes/api/missingItem.php'));
});
