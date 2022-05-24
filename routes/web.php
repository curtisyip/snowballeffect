<?php

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

Route::get('/', 'ScheduleController@index');

Route::post('ajax/roster_load', 'ScheduleController@load');
Route::post('ajax/roster_store', 'ScheduleController@store');
Route::post('ajax/roster_remove', 'ScheduleController@remove');
