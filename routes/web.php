<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FertiliserInventoryApplicationController;

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
    return view('add-fertiliser-application-form');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('add-fertiliser-application-form', [FertiliserInventoryApplicationController::class, 'index']);
Route::post('apply', [FertiliserInventoryApplicationController::class, 'apply']);
