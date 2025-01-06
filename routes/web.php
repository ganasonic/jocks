<?php

use Illuminate\Support\Facades\Auth;
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

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

Route::get('/', 'HomeController@index')->name('index');
Route::get('/index', 'HomeController@index')->name('index');
Route::get('/home', 'HomeController@index')->name('index');

//シフト管理
//マイシフト
Route::get('/myshift', 'ShiftController@myshift')->name('myshift');
Route::post('/shiftupdate/{id}', 'ShiftController@shiftupdate')->name('shiftupdate');
Route::get('/datechange/{date}', 'ShiftController@datechange')->name('datechange');
//当日詳細
Route::get('/detail', 'ShiftController@detail')->name('detail');
//指定日詳細
Route::get('/specified/{date}', 'ShiftController@specified')->name('specified');
//食事集計
Route::get('/meal', 'ShiftController@meal')->name('meal');
//月間シフト
Route::get('/monthly', 'ShiftController@monthly')->name('monthly');
//集計
Route::get('/totalling', 'ShiftController@totalling')->name('totalling');
//従食確認
Route::get('/lunch', 'ShiftController@lunch')->name('lunch');
//寮食確認
Route::get('/dormitorymeal', 'ShiftController@dormitorymeal')->name('dormitorymeal');
//宿泊確認
Route::get('/dormitorystay', 'ShiftController@dormitorystay')->name('dormitorystay');
//送迎確認
Route::get('/pickupbus', 'ShiftController@pickupbus')->name('pickupbus');
