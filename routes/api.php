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

Route::post('/TranslateFile', 'FileInterpretationController@translateVtt');
Route::get('/test', 'FileInterpretationController@splitFile');
/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/Register', 'Api\AuthController@Register');
Route::post('/Login', 'Api\AuthController@Login');
Route::get('/TestAuth', 'Api\AuthController@TestAuth')->middleware('auth:api');

/*
|--------------------------------------------------------------------------
| Transcription
|--------------------------------------------------------------------------
*/
Route::post('/Transcription','Api\TranscriptionController@TranscribeAudio');



/*
|--------------------------------------------------------------------------
| SECTION
|--------------------------------------------------------------------------
*/


