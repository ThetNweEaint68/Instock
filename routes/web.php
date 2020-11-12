<?php

use Illuminate\Support\Facades\Route;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\ImportantStockUpdate;

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

//Route::get('/', function () {
    //return view('welcome');

Route::get('/mail-preview', function () {
    $user = factory(\App\Models\User::class)->create();

    return (new ImportantStockUpdate(Stock::first()))->toMail($user);
});