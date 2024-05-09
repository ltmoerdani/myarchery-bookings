<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});


// API Data Master
Route::get('get-country', 'BackEnd\Region\RegionController@getCountry')->name('get.region.country');
Route::get('get-state/{id}', 'BackEnd\Region\RegionController@getState')->name('get.region.state');
Route::get('get-city/{id_country}/{id_state}', 'BackEnd\Region\RegionController@getCity')->name('get.region.city');

Route::get('get-clubs', 'BackEnd\ClubController@getClubs')->name('get.clubs');
Route::get('get-comp-type', 'BackEnd\Event\EventController@getCompetitionType')->name('get.competition.type');
Route::get('get-delegation', 'BackEnd\DelegationController@getDelegationType')->name('get.delegation.type');
Route::get('generate-code', 'BackEnd\Event\EventController@codeGenerate')->name('generate.code.event');

// delete soon
Route::post('event-store', 'BackEnd\Event\EventController@store')->name('admin.event_management.store_event');