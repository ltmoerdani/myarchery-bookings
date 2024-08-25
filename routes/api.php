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
Route::get('s2-get-country', 'BackEnd\Region\RegionController@s2GetCountry')->name('get.region.s2-country');
Route::get('get-state/{id}', 'BackEnd\Region\RegionController@getState')->name('get.region.state');
Route::get('get-city/{id_country}/{id_state}', 'BackEnd\Region\RegionController@getCity')->name('get.region.city');
Route::get('get-city/{id_country}', 'BackEnd\Region\RegionController@getCity')->name('get.region.city');
Route::get('s2-get-city', 'BackEnd\Region\RegionController@s2GetCity')->name('get.region.s2-city');
Route::get('s2-get-city/{id_country}', 'BackEnd\Region\RegionController@s2GetCity')->name('get.region.s2-city');
Route::get('s2-get-city/{id_country}/{id_state}', 'BackEnd\Region\RegionController@s2GetCity')->name('get.region.s2-city');
Route::get('s2-get-delegation-type', 'FrontEnd\DelegationTypeController@s2GetDelagationType')->name('get.s2-delegation-type');

Route::get('get-clubs', 'BackEnd\ClubController@getClubs')->name('get.clubs');
Route::get('get-comp-type', 'BackEnd\Event\EventController@getCompetitionType')->name('get.competition.type');
Route::get('get-delegation', 'BackEnd\DelegationController@getDelegationType')->name('get.delegation.type');
Route::get('get-org', 'BackEnd\OrganizationController@getListOrganization')->name('get.list.organization');
Route::get('generate-code', 'BackEnd\Event\EventController@codeGenerate')->name('generate.code.event');
Route::get('check-code-event', 'BackEnd\Event\EventController@checkCodeEvent')->name('check.code.event');

Route::post('xendit/callback-disbursement', 'FrontEnd\PaymentGateway\XenditController@callback_disbursement')->name('xendit_callback_disbursement');
