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

Route::get('/', function () {
    return view('welcome');
});

Route::resource('posts','PostsController');

Route::get('/home', 'HomeController@index')->name('home');

/**
 * for login
 */
Route::get('login', function() {return view('auth.login', [
    'info' => session('info')
])->withErrors(session('myerror'));})->name('login');
Route::post('login', 'Auth\LoginController@login');

/**
 * for logout
 */
Route::get('logout', 'Sentinel\SentinelController@logout');

/**
 * for registration
 */
Route::get('register', function() {return view('auth.register');})->name('register');
Route::post('register', 'Auth\RegisterController@register');
Route::get('register/{email}', 'Sentinel\SentinelController@resendActivationCode');
/**
 * for activation
 */
Route::get('activate/{email}/{code}', 'Sentinel\ActivateController@activate');

/**
 * for logout
 */
Route::match(['get', 'post'], 'logout', 'Auth\LoginController@logout')->name('logout');

/**
 * for password reset
 */
Route::get('password/reset/{email}/{code}/{password}', 'Sentinel\SentinelController@resetPassword');
Route::get('password/reset', function() {return view('auth.passwords.reset', ['token'=>'']);});
Route::post('password/reset', 'Sentinel\SentinelController@sendResetPassword');