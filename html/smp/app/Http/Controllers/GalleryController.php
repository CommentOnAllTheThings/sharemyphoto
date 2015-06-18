<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

/*
	Description: Our image gallery controller which retrieves, uploads and deletes images on the site
*/
class GalleryController extends Controller {
	/*
		Description: Gets the target page from the gallery

		@param page_number The target page number we want to retrieve.
		@returns 
	*/
	public function showPage($page_number = 1) {
		// Check if the page_number is set and numeric! 
		if (!isset($page_number) || !is_numeric($page_number))
			abort(404);

		// Explicitly cast the page_number to an integer (in case, for some strange reason a float, double etc. is passed in -.-)
		$page_number = (int)$page_number;

		// Last check to ensure that the page_number is greater than 0 -- ie. 1 or higher!
		if ($page_number < 1)
			abort(404);

		// Retrieve the page

		// Pass back the data
		return 'GalleryController is showing page '.$page_number;
	}

	/*
		Description: Sends the client the uploader view.
		
		@returns The uploader view.
	*/
	public function showUploader() {
		return view('uploader');
	}

	/*
		Description: Uploads an image from the client's computer to the server.

		@param TO DO
		@returns TO DO
	*/
	public function uploadImage() {
		return '';
	}

	/*
		Description: Loads the image file page to show a larger version of the image.

		@param guid The image's unique identifier.
		@returns TO DO
	*/
	public function showImage($guid) {
		// TO DO
		return view('showimage', ['image_path' => '/image/to/do']);
	}

	/*
		Description: Retrieves the image file from the server.

		@param guid The image's unique identifier.
		@returns TO DO
	*/
	public function getImage($guid) {
		return '';
	}

	/*
		Description: Marks an image or a set of images as deleted on the server.

		@param TO DO
		@returns TO DO
	*/
	public function deleteImage() {
		return '';
	}
}
