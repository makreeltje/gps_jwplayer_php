<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TRANSLATION
|--------------------------------------------------------------------------
*/
Route::post('/TranslateFile', 'Api\TranslationController@translateVtt')->middleware('auth:api');

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
Route::get('/checkAuthorization', 'Api\AuthController@checkAuthorization')->middleware('auth:api');
Route::get('/Logout', 'Api\AuthController@Logout');

/*
|--------------------------------------------------------------------------
| USERS
|--------------------------------------------------------------------------
*/
Route::get('/GetUserById', 'Api\UserController@GetUserById')->middleware('auth:api');
Route::get('/GetAllUsers', 'Api\UserController@GetAllUsers')->middleware('auth:api');
Route::put('/UpdateUserById', 'Api\UserController@UpdateUserById')->middleware('auth:api');
Route::delete('/DeleteUserById', 'Api\UserController@DeleteUserById')->middleware('auth:api');

/*
|--------------------------------------------------------------------------
| TRANSCRIPTION
|--------------------------------------------------------------------------
*/
Route::post('/Transcription','Api\TranscriptionController@TranscribeAudio');

/*
|--------------------------------------------------------------------------
| CAPTIONS
|--------------------------------------------------------------------------
*/
Route::post('/UploadCaption', 'Api\VttController@UploadCaption')->middleware('auth:api');
Route::get('/GetCaption', 'Api\VttController@GetCaption')->middleware('auth:api');
Route::post('/SaveCaption', 'Api\VttController@SaveCaption')->middleware('auth:api');
Route::delete('/DeleteCaption', 'Api\VttController@DeleteCaption')->middleware('auth:api');



