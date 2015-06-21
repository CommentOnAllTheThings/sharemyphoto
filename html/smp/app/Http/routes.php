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
    'as' => 'gallery_root',
    'uses' => 'GalleryController@showPage'
]);

Route::get('/gallery', [
    'as' => 'gallery_main',
    'uses' => 'GalleryController@showPage'
]);

Route::get('/gallery/page/{page}', [
    'as' => 'gallery_pagination',
    'uses' => 'GalleryController@showPage'
])->where('page', '[1-9][0-9]*');

Route::get('/image/upload', [
    'as' => 'gallery_uploader',
    'uses' => 'GalleryController@showUploader'
]);

Route::get('/image/upload/progress', [
    'as' => 'gallery_uploader_progress',
    'uses' => 'GalleryController@getProgress'
]);

Route::post('/image/upload/save', [
	'as' => 'gallery_uploader_upload',
    'uses' => 'GalleryController@uploadImage'
]);

Route::get('/image/delete/{guid}/{key}', [
	'as' => 'gallery_confirm_deletion',
	'uses' => 'GalleryController@showDeleteImageConfirmation'
])->where(['guid' => '[A-Za-z0-9]+', 'key' => '[A-Za-z0-9]+']);

Route::post('/image/delete/{guid}/{key}', [
	'as' => 'gallery_confirm_deletion',
	'uses' => 'GalleryController@deleteImage'
])->where(['guid' => '[A-Za-z0-9]+', 'key' => '[A-Za-z0-9]+']);

Route::get('/image/view/{guid}', [
	'as' => 'gallery_view',
	'uses' => 'GalleryController@showImage'
])->where('guid', '[A-Za-z0-9]+');

Route::get('/image/get/{guid}', [
	'as' => 'gallery_retrieve',
	'uses' => 'GalleryController@getImage'
])->where('guid', '[A-Za-z0-9]+');

Route::get('/image/get/thumbnail/{guid}', [
	'as' => 'gallery_retrieve_thumb',
	'uses' => 'GalleryController@getThumbnail'
])->where('guid', '[A-Za-z0-9]+');

Route::post('/image/delete', [
	'as' => 'gallery_mass_delete',
	'uses' => 'GalleryController@massDeleteImages'
]);