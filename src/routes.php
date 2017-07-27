<?php


Route::group(['prefix'=> 'leapfrog', 'namespace' => 'JimHlad\LeapFrog\Controllers'], function () {
	Route::get('/', 'DashboardController@index')->name('leapfrog.dashboard');
	Route::get('/crud', 'CrudController@index')->name('leapfrog.crud');
	Route::post('/crud/generate', 'CrudController@generate')->name('leapfrog.crud.generate');
});