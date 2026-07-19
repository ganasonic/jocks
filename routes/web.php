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

// 体調管理機能のルーティング
Route::prefix('conditions')->name('conditions.')->group(function () {
    Route::get('/list', 'DailyConditionController@indexlist')->name('indexlist'); // 一覧画面
    Route::get('/', 'DailyConditionController@index')->name('index'); // 一覧画面
    Route::get('/create', 'DailyConditionController@create')->name('create'); // 入力画面
    Route::post('/', 'DailyConditionController@store')->name('store'); // 保存処理

    // ⬇️ この2行を追記
    Route::get('/{date}/edit', 'DailyConditionController@edit')->name('edit');     // 編集画面
    Route::patch('/{date}', 'DailyConditionController@update')->name('update');    // 更新処理
});
// プロフィール機能のルーティング
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', 'UserDetailController@show')->name('show');     // プロフィール表示・編集画面
    Route::post('/', 'UserDetailController@update')->name('update'); // 保存・更新処理
});

// 目標管理（PDCA）機能のルーティング
Route::prefix('goals')->name('goals.')->group(function () {
    Route::get('/', 'GoalController@index')->name('index');          // 一覧
    Route::get('/create', 'GoalController@create')->name('create');    // 新規作成
    Route::post('/', 'GoalController@store')->name('store');          // 保存
    Route::get('/{goal}/edit', 'GoalController@edit')->name('edit');  // 振り返り・編集
    Route::patch('/{goal}', 'GoalController@update')->name('update'); // 更新
});

// トレーニング管理機能のルーティング
Route::prefix('trainings')->name('trainings.')->group(function () {
    Route::get('/', 'TrainingController@index')->name('index');        // 一覧
    Route::get('/create', 'TrainingController@create')->name('create'); // 作成
    Route::post('/', 'TrainingController@store')->name('store');        // 保存
});
