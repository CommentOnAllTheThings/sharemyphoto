<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

/*
	Description: Our image gallery controller which retrieves the list of images to display on the site
*/
class GalleryController extends Controller {

	/*
		Description: Gets the target page from the gallery

		@param page_number The target page number we want to retrieve
		@returns null on failure, array containing images on success
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
}
