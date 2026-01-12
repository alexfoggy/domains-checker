<?php

use Illuminate\Support\Facades\Route;

Route::get('/check', 'TestController@index');
\Illuminate\Support\Facades\Auth::routes();
// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
