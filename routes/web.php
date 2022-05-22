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

Route::get('/',[\App\Http\Controllers\AtecoController::class,"home"]);
Route::get('/codice/{code}',[\App\Http\Controllers\AtecoController::class,"showCode"]);


Route::get('/test/{code}',[\App\Http\Controllers\AtecoController::class,"testCode"]);
