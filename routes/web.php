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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/uploads/{search}', function($search) {
    $search = trim(strip_tags($search), "<>$#&*\n\t!()?^@|");
    return response()->download(storage_path("app/public/uploads/$search"));
})->where('search', '.*');
