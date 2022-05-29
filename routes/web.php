<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('seed')->group(function() {
    Route::get('product', [App\Http\Controllers\SeederController::class, 'productSeeder']);
    Route::get('report', [App\Http\Controllers\SeederController::class, 'reportSeeder']);
});

Route::get('login', [App\Http\Controllers\LoginController::class, 'showLogin'])->name('login');
Route::post('login', [App\Http\Controllers\LoginController::class, 'login']);

Route::get('logout', [App\Http\Controllers\LoginController::class, 'logout']);

Route::middleware('auth')->group(function() {
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);
    Route::get('/dataset', [\App\Http\Controllers\HomeController::class, 'listData']);
    Route::get('/dataset-{slug}', [\App\Http\Controllers\HomeController::class, 'detailData']);
    Route::get('/chart', [\App\Http\Controllers\HomeController::class, 'chart']);
});


