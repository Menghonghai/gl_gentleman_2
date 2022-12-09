<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
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
//App::setLocale('kh');
// session(['local' => 'kh']);
Route::get('/', function () {
    //return view('welcome');
    return view('home');
    
});
Route::get('/product', function () {
    //return view('welcome');
    return view('app.products.product-alt');

});

Auth::routes();
// Route::group(['prefix' => 'acc'], function () {

    // Route::any('/{controller}/{method?}/{id?}', ['uses' => 'App\Http\Controllers\UserAccessController@index'])
    //     ->where(['id' => '[0-9a-zA-Z]+', 'controller' => '[0-9a-z]+', 'method' => '[_a-z]+'])->name('admin.controller');
// Route::group(['middleware' => ['auth']], function () {

//     Route::group(['middleware' => ['checkuserpemission']], function () {
//     });
// });	
// });
