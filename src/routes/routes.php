<?php 

Route::post('me', 'AuthController@index');
Route::post('login', 'AuthController@create');
Route::post('logout', 'AuthController@destroy');
Route::post('refresh', 'AuthController@update');
