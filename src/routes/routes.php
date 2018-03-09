<?php 

Route::post('login', 'AuthController@create');
Route::post('refresh', 'AuthController@update');
