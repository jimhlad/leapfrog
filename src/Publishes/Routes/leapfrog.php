<?php

// Note: You should ensure that these routes are only accessible within the local environment
Route::group(['prefix'=> 'leapfrog'], function () {
	Route::get('/', '\JimHlad\LeapFrog\Controllers\DashboardController@index')->name('leapfrog.dashboard');
	Route::get('/crud', '\JimHlad\LeapFrog\Controllers\CrudController@index')->name('leapfrog.crud');
	Route::post('/crud/generate', '\JimHlad\LeapFrog\Controllers\CrudController@generate')->name('leapfrog.crud.generate');
});