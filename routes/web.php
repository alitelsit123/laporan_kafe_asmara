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

Route::get('login', [App\Http\Controllers\LoginController::class, 'showLogin'])->name('login');
Route::post('login', [App\Http\Controllers\LoginController::class, 'login']);

Route::get('logout', [App\Http\Controllers\LoginController::class, 'logout']);

Route::middleware('auth')->group(function() {
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);
    Route::get('/dataset', [\App\Http\Controllers\HomeController::class, 'listData']);
    Route::get('/chart', [\App\Http\Controllers\HomeController::class, 'chart']);
});
