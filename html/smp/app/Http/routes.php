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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/show/{page}', function ($page) {
    return 'Showing Page '.$page;
});

Route::get('/upload', function(){
	return 'Uploader';
});

Route::get('/delete', function(){
	return 'Delete';
});

Route::post('/delete', function(){
	return 'POST Delete';
});

Route::get('/view/{id}', function($id){
	return 'Viewing '.$id;
});

Route::get('/login', function(){
	return 'Login Form';
});

Route::post('/login', function(){
	return 'Authenticating...';
});

Route::get('/logout', function(){
	return 'Logout';
});