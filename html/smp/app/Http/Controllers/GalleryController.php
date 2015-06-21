<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Validator;
use Storage;

// Our Image Model
use App\Models\Image;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/*
	Description: Our image gallery controller which retrieves, uploads and deletes images on the site
*/
class GalleryController extends Controller {
	const IMAGE_PATH = 'images';

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

		// Read in the POST data and substitute any default values
		$title = $request->input('upload-title', 'No Title');
		$description = $request->input('upload-description', '');
		$file = $request->file('upload-file');

		// Create our array of data to test in the validator
		$validator_data = array();
		$validator_data['imagetitle'] = $title;
		$validator_data['imagefile'] = $file;

		// Create our validator and test both the title and image
		$validator = validator::make($validator_data, [
			'imagetitle' => 'required|string',
			'imagefile' => 'required|image',
		]);

		// Did validation pass on the image and title?
		if ($validator->fails()) {
			// No, either they didn't choose an image, the file is of the wrong type or the title is not valid!
			// Send the user back to the image upload page
			return redirect()->route('gallery_uploader')->with('message', 'Please ensure you have entered a valid image title and image file!');
		}

		if (!$file->isValid()) {
			// Send the user back to the image upload page
			return redirect()->route('gallery_uploader')->with('message', 'Invalid image file! Please try again.');
		}

		// Validation is ok, keep going!
		// Get the file extension
		$file_extension = $file->getClientOriginalExtension();

		// Determine what length GUID we should generate
		// Minimum length is 8 characters, maximum is 32
		$guid_length = mt_rand(8, 32);

		// Generate random GUID of length $guid_length
		$generated_guid = str_random($guid_length);

		// Generate random delete key
		$generated_delete_key = str_random(128);

		// Variable to keep track of the currently logged in user id (default for anonymous users/not logged in is 0!)
		$user_id = 0;

		// Get the current logged in user's id
		// TO DO

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

        // Success, now manipulate the file!
		if ($creation_success) {
			// The name of the image (GUID + . + EXTENSION)
			$image_name = sprintf('%s.%s', $generated_guid, $file_extension);

			// Generate the target path to save the image file to
			$target_image_path = sprintf('%s/app/%s/%d', storage_path(), GalleryController::IMAGE_PATH, $user_id);

			// Generate the full path to the image file
			$target_image_full_path = sprintf('%s/%d/%s', GalleryController::IMAGE_PATH, $user_id, $image_name);

			// Move the file from the temporary directory to where we want it under storage/images/[id]/...
			$save_sucessful = $file->move($target_image_path, $image_name);

			// Did we save it successfully?
			if ($save_sucessful) {
				// Yes
				// Update the path in the database
				$image->image_file_path = $target_image_full_path;
				$db_save_successful = $image->save();

				// Only set $image_upload_successful to true if we updated the image path!
				if ($db_save_successful) {
					$image_upload_successful = true;
				}
				else {
					// Send the user back to the image upload page
					return redirect()->route('gallery_uploader')->with('message', 'We couldn\'t save your image. Please try again later.');
				}
			}
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
		// Check if the GUID is set and >0 characters
		if (isset($guid) && strlen($guid) > 0) {
			try {
				$image_information = Image::getImageInformationFromGUID($guid);
				if ($image_information['status'] == 1) {
					// Create the array of parameters to house the path to the image file
					$view_parameters = array();
					$view_parameters['image_title'] = $image_information['title'];
					$view_parameters['image_path'] = sprintf('/image/get/%s', $guid);

					// Send back the view
					return view('showimage', $view_parameters);
				}
				else {
					// Image is not "Published" (ie. deleted etc.)
				}
			}
			catch (ModelNotFoundException $e) {
				// Image does not exist!
			}
		}
		else {
			// No GUID specified! We will 404 here in case someone may have made a change like making GUID optional, perhaps?
		}

		// Default behaviour is to fail unless the image exists
		abort(404);
	}

	/*
		Description: Retrieves the image file from the server.

		@param guid The image's unique identifier.
		@returns TO DO
	*/
	public function getImage($guid) {
		// Check if the GUID is set and >0 characters
		if (isset($guid) && strlen($guid) > 0) {
			try {
				$image_information = Image::getImageInformationFromGUID($guid);
				if ($image_information['status'] == 1) {
					// Retrieve our local disk
					$local_disk = Storage::disk('local');

					// Check if the file path is not empty or null
					if (isset($image_information['file_path']) && strlen($image_information['file_path']) > 0) {
						// Check if the file exists
						if ($local_disk->exists($image_information['file_path'])) {
							// Get the file MINE type
							$file_mime_type = $local_disk->mimeType($image_information['file_path']);

							// Send the image data along with the Content-Type header to tell the browser the MIME type
							return response($local_disk->get($image_information['file_path']), 200)->header('Content-Type', $file_mime_type);
						}
						else {
							// File doesn't exist
						}
					}
					else {
						// Image path is empty or null!
					}
				}
				else {
					// Image is not "Published" (ie. deleted etc.)
				}
			}
			catch (ModelNotFoundException $e) {
				// Image does not exist!
			}
		}
		else {
			// No GUID specified! We will 404 here in case someone may have made a change like making GUID optional, perhaps?
		}

		// Default behaviour is to fail unless the image exists
		abort(404);
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
