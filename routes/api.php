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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware'=>['auth:api'], 'namespace'=>'Api'], function () {
    Route::resource('roles', 'RoleController');
    Route::resource('users', 'UserController');

    Route::get('/verify', 'UserController@verify'); //to verify user exist so we route to usercontroller and run verify function

    Route::post('email/verify','UserController@verifyEmail'); // to verify that email is not already use so we run the verifyEmail function

    Route::post('users/role', 'UserController@updateUserRole');//  to update user's role

    Route::post('users/photo', 'UserController@changePhoto');// to upload photos

    Route::post('roles/delete', 'RoleController@deleteAll');
    Route::post('users/delete', 'UserController@deleteAll');
});

Route::post('login', 'Api\UserController@login')->name('login');
Route::post('signup', 'Api\UserController@signup')->name('signup');

