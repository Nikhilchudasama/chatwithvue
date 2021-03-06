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

Route::get('/', 'HomeController@index')->name('home');

Route::post('/login', 'HomeController@login')->name('login');

Route::get('/check-user-logged-in', 'HomeController@checkUserLoggedIn')->name('check_user_logged_in');

Route::post('/send-message', 'HomeController@sendMessage')->name('send_message');
