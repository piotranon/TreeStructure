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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', "AuthController@login");
Route::post('/register', "AuthController@register");

//Node requests
Route::middleware('auth:sanctum')->get('/node/all', "NodeController@getForUser");
Route::middleware('auth:sanctum')->get('/node', "NodeController@getNode");
Route::middleware('auth:sanctum')->get('/node/nameMapping', "NodeController@getNameMapping");

Route::middleware('auth:sanctum')->post('/node', "NodeController@storeNode");
Route::middleware('auth:sanctum')->post('/node/changeOrder', "NodeController@changeOrder");

Route::middleware('auth:sanctum')->delete('/node/{id}', "NodeController@destroy");

Route::middleware('auth:sanctum')->patch('/node', "NodeController@update");
