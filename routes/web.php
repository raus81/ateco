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
Route::get('/about-us',[\App\Http\Controllers\AtecoController::class,"aboutUs"]);

Route::get('/codice/{code}',[\App\Http\Controllers\AtecoController::class,"showCode"]);
Route::get('/immagini/svg/{code}',[\App\Http\Controllers\AtecoController::class,"showImage"]);
Route::get('/faq',function(){
    return view('faq');
});
Route::get('/errori-comuni',function(){
    return view('info.erroricomuni');
});
Route::get('/codice-corretto',function(){
    return view('info.codice-adatto');
});

Route::get('/esempi-utilizzo',function(){
    return view('info.esempi-utilizzo');
});



Route::get('/test/{code}',[\App\Http\Controllers\AtecoController::class,"testCode"]);
