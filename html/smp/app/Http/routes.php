<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', [
    'as' => 'gallery',
    'uses' => 'GalleryController@showPage'
]);

Route::get('/show/{page}', [
    'as' => 'gallery_pagination',
    'uses' => 'GalleryController@showPage'
])->where('page', '[1-9][0-9]*');

Route::get('/upload', function(){
	return 'Uploader';
});

Route::put('/upload', function(){
	return 'Uploader';
});

Route::get('/delete', function(){
	return 'Delete';
});

Route::post('/delete', function(){
	return 'POST Delete';
});

Route::get('/view/{guid}', [
	'as' => 'gallery_view',
	'uses' => 'GalleryController@showImage'
])->where('guid', '[A-Za-z0-9]+');

Route::get('/get/{guid}', [
	'as' => 'gallery_retrieve',
	'uses' => 'GalleryController@getImage'
])->where('guid', '[A-Za-z0-9]+');

/* TO DO
	Route::get('/login', function(){
		return 'Login Form';
	});

	Route::post('/login', function(){
		return 'Authenticating...';
	});

	Route::get('/logout', function(){
		return 'Logout';
	});
*/