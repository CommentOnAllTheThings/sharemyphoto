<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Image;
use Validator;

/*
	Description: Our image gallery controller which retrieves, uploads and deletes images on the site
*/
class GalleryController extends Controller {
	/*
		Description: Gets the target page from the gallery

		@param page_number The target page number we want to retrieve.
		@returns The gallery view.
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

		// TO DO -- Update highest page number and retrieve list of images!
		$highest_page = 1;

		// TO DO
		$images = Image::all();

		// 404 if the page number exceeds the highest page
		if ($page_number > $highest_page)
			abort(404);

		// Initialize parameters to pass to gallery view
		$view_parameters = array();

		// Save current page number
		$view_parameters['current_page'] = $page_number;

		// Save max (ie. highest) page number
		$view_parameters['highest_page'] = $highest_page;

		// Process the view
		return view('gallery', $view_parameters);
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
	public function uploadImage(Request $request) {
		// Flag to determine if the image upload was successful
		$image_upload_successful = false;

		// The image unique identifier (GUID)
		$image_guid = '';

		// The image delete key
		$image_delete_key = '';

		$title = $request->input('upload-title', 'No Title');
		$description = $request->input('upload-description', '');
		$file = $request->file('upload-file');
		// Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));

		// Create our array of data to test in the validator
		$validator_data = array('imagefile' => $file);

		// Create our validator and test it as an image
		$validator = validator::make($validator_data, [
			'imagefile' => 'required|image'
		]);

		// Did validation pass on the image?
		if ($validator->fails()) {
			// No, either they didn't choose an image or the file is of the wrong type!
			// Send the user back to the image upload page
			return redirect()->route('gallery_uploader')->with('message', 'Please select an image file to upload.');
		}

		// Validation is ok, keep going!
		// Generate random GUID
		$generated_guid = str_random(32);

		// Generate random delete key
		$generated_delete_key = str_random(128);

		// Create a new entry in our image table (smp_images)
		$image = new Image;
		// Set the values to map to the table columns
        $image->image_guid = $generated_guid;
        $image->image_title = $title;
        $image->image_description = $description;
        $image->image_status = 1;
        $image->image_delete_key = $generated_delete_key;
        // TO DO -- If and when you do implement users, you can set user_id HERE!
        $creation_success = $image->save();

        if ($creation_success) {
        	$image_guid = $generated_guid;

        	// Process the file

        }

		if (!$image_upload_successful) {
			// Send the user back to the image upload page
			return redirect()->route('gallery_uploader')->with('message', 'Image could not be uploaded! Please try again later.');
		}
		else {
			// Create the array of parameters to house the GUID
			$view_parameters = array();
			$view_parameters[] = $image_guid;

			// Send the user to the image viewer page
			return redirect()->route('gallery_view', $view_parameters)->with('message', 'Image uploaded successfully!');
		}
	}

	/*
		Description: Returns the upload progress.

		@param TO DO
		@returns TO DO
	*/
	public function getProgress() {
		// TO DO
		return ['progress' => '50'];
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
	public function showDeleteImageConfirmation($guid, $key) {
		return '';
	}

	/*
		Description: Marks an image or a set of images as deleted on the server.

		@param TO DO
		@returns TO DO
	*/
	public function deleteImage($guid, $key) {
		return '';
	}
}
