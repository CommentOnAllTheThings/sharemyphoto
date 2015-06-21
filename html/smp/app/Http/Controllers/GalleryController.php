<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Validator;
use Storage;

// Our Image Model
use App\Models\Image;

// Use Intervention Image
use Intervention\Image\Facades\Image as Img;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/*
	Description: Our image gallery controller which retrieves, uploads and deletes images on the site
*/
class GalleryController extends Controller {
	const IMAGE_PATH = 'images';
	const IMAGES_PER_PAGE = 12;

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

		// Flag to determine if we should just show an error page
		$no_images_available = true;

		// How many images have been uploaded?
		$number_images = 0;

		// TO DO -- Update highest page number and retrieve list of images!
		$highest_page = 1;

		// Create our array to hold our images
		$image_list = array();

		// Get the number of images
		$images = Image::getNumberOfImages();
		if (isset($images) && count($images) > 0) {
			// Get the count
			$number_images = $images[0]->number_images;

			// One or more images
			if ($number_images > 0) {
				$no_images_available = false;

				// Calculate the number of pages
				$number_of_pages = (int)($number_images / GalleryController::IMAGES_PER_PAGE);

				// Calculate the number of images that don't make IMAGES_PER_PAGE
				$number_of_overflow_images = $number_images % GalleryController::IMAGES_PER_PAGE;

				// When there are overflow images, we need to add an extra page to house them!
				if ($number_of_overflow_images > 0)
					$number_of_pages = $number_of_pages + 1;

				// Save the number of pages to the highest page argument
				$highest_page = $number_of_pages;

				// Calculate the image offset
				$image_offset = ($page_number - 1) * GalleryController::IMAGES_PER_PAGE;

				// Get the list of images
				$image_list = Image::getImages($image_offset, GalleryController::IMAGES_PER_PAGE);
			}
		}

		// 404 if the page number exceeds the highest page
		if ($page_number > $highest_page)
			abort(404);

		// If there are no images available show an error message
		if ($no_images_available) {
			return view('gallery', ['no_images' => true]);
		}

		// Determine what we will need to show in the pagination control
		// Keep track of the low and max page numbers!
		$min_page = 1;
		$max_page = 1;

		// Determine the lowest page number
		if ($page_number > 3) {
			// Lowest Page can be up to 2 pages before
			$min_page = $page_number - 2;

			// Highest Page can be up to 2 page after the current page
			$page_delta = $highest_page - $page_number;

			// Check to see what the max we should display is
			if ($page_delta >= 0) {
				switch ($page_delta) {
					// Less than two
					case 1:
					case 0:
						if ($highest_page > 4)
							$min_page = $highest_page - 4;
						$max_page = $highest_page;
						break;
					// Two or more!
					default:
						$max_page = $page_number + 2;
						break;
				}
			}
			else // Negative, not sure how this is possible...either way, clamp to highest page!
				$max_page = $highest_page;
		}
		else {
			if ($highest_page <= 5) {
				// Highest page is 5
				$max_page = $highest_page;
			}
			else {
				// Cap at 5
				$max_page = 5;
			}
		}

		// Initialize parameters to pass to gallery view
		$view_parameters = array();

		// Set the parameter to signify that we have images -- I apologise it's a double negative! >.<
		$view_parameters['no_images'] = false;

		// Save current page number
		$view_parameters['current_page'] = $page_number;

		// Save max (ie. highest) page number
		$view_parameters['highest_page'] = $highest_page;

		// Save the number of images hosted
		$view_parameters['images_uploaded'] = $number_images;

		// Save the lowest page number to show in the pagination control
		$view_parameters['min_page'] = $min_page;

		// Save the highest page number to show in the pagination control
		$view_parameters['max_page'] = $max_page;

		// Save the list of images to be displayed on the page
		$view_parameters['list_images'] = $image_list;

		// Process the view
		return view('gallery', $view_parameters);
	}

	/*
		Description: Sends the client the uploader view.
		
		@returns The uploader view.
	*/
	public function showUploader() {
		// Return the uploader view.
		return view('uploader');
	}

	/*
		Description: Uploads an image from the client's computer to the server.

		@param request The HTTP request.
		@returns A redirect to either the image page or back to the upload page.
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

		// Check if the file is valid...
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
		$image_guid = str_random($guid_length);

		// Generate random delete key
		$generated_delete_key = str_random(128);

		// Variable to keep track of the currently logged in user id (default for anonymous users/not logged in is 0!)
		$user_id = 0;

		// Get the current logged in user's id
		// TO DO

		// Create a new entry in our image table (smp_images)
		$image = new Image;
		// Set the values to map to the table columns
		$image->image_guid = $image_guid;
		$image->image_title = $title;
		$image->image_description = $description;
		$image->image_status = 1;
		$image->image_delete_key = $generated_delete_key;
		// TO DO -- If and when you do implement users, you can set user_id HERE!
		$creation_success = $image->save();

		// Success, now manipulate the file!
		if ($creation_success) {
			// The name of the image (GUID + . + EXTENSION)
			$image_name = sprintf('%s.%s', $image_guid, $file_extension);

			// The name of the thumbnail image (thumb_ + GUID + . + EXTENSION)
			$image_thumb_name = 'thumb_'.$image_name;

			// Generate the path to the folder housing all of the images
			$image_folder = sprintf('%s/app/%s/%d', storage_path(), GalleryController::IMAGE_PATH, $user_id);

			// Generate the path relative to the storage/app folder
			$image_relative_path = sprintf('%s/%d', GalleryController::IMAGE_PATH, $user_id);

			// Generate the relative and full path to the image file
			$image_file_relative_path = sprintf('%s/%s', $image_relative_path, $image_name);
			$image_file_full_path = sprintf('%s/%s', $image_folder, $image_name);

			// Generate the relative and full path to the thumbnail file
			$image_thumb_relative_path = sprintf('%s/%s', $image_relative_path, $image_thumb_name);
			$image_thumb_full_path = sprintf('%s/%s', $image_folder, $image_thumb_name);

			// Move the file from the temporary directory to where we want it under storage/images/[id]/...
			$save_sucessful = $file->move($image_folder, $image_name);

			// Make a thumbnail of the image
			try {
				$image_resize = Img::make($image_file_full_path)->fit(100, 100);
				$image_resize->save($image_thumb_full_path);
			}
			catch (NotReadableException $e) { }

			// Did we save it successfully?
			if ($save_sucessful) {
				// Yes
				// Update the image and thumbnail path in the database
				$image->image_file_path = $image_file_relative_path;
				$image->image_thumb_path = $image_thumb_relative_path;
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
		return ['progress' => '0'];
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
					// Copy the title
					$view_parameters['image_title'] = $image_information['title'];
					// Copy the image path
					$view_parameters['image_path'] = sprintf('/image/get/%s', $guid);
					// Don't show the confirm delete form!
					$view_parameters['confirm_delete'] = false;

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
		Description: Retrieves the thumbnail image file from the server.

		@param guid The image's unique identifier.
		@returns The image thumbnail or forces a 404 when the GUID doesn't exist.
	*/
	public function getThumbnail($guid) {
		// Invoke the getImage method, and pass in true for $thumbnail to retrieve the thumbnail image
		return $this->getImage($guid, true);
	}

	/*
		Description: Retrieves the full size image file or thumbnail (if $thumbnail == true) from the server.

		@param guid The image's unique identifier.
		@returns The image or image thumbnail. Forces a 404 when the GUID doesn't exist.
	*/
	public function getImage($guid, $thumbnail = false) {
		// Check if the GUID is set and >0 characters
		if (isset($guid) && strlen($guid) > 0) {
			try {
				$image_information = Image::getImageInformationFromGUID($guid);
				if ($image_information['status'] == 1) {
					// Retrieve our local disk
					$local_disk = Storage::disk('local');

					// Determine which image file we need, default is the full size image
					$use_image_path = $image_information['file_path'];

					// Should we get the thumbnail instead?
					if ($thumbnail === true) {
						$use_image_path = $image_information['thumb_path'];
					}

					// Check if the file path is not empty or null
					if (isset($use_image_path) && strlen($use_image_path) > 0) {
						// Check if the file exists
						if ($local_disk->exists($use_image_path)) {
							// Get the file MINE type
							$file_mime_type = $local_disk->mimeType($use_image_path);

							// Send the image data along with the Content-Type header to tell the browser the MIME type
							return response($local_disk->get($use_image_path), 200)->header('Content-Type', $file_mime_type);
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
		Description: Asks the user to confirm that they want to delete the image.

		@param guid The image unique identifier.
		@param key The image deletion key.
		@returns The image deletion view.
	*/
	public function showDeleteImageConfirmation($guid, $key) {
		// Check if the GUID is set and >0 characters
		if (isset($guid, $key) && strlen($guid) > 0 && strlen($key) > 0) {
			try {
				$image_information = Image::getImageInformationFromGUID($guid);
				if ($image_information['status'] == 1) {
					// Create the array of parameters to house the path to the image file
					$view_parameters = array();
					// Copy the title
					$view_parameters['image_title'] = $image_information['title'];
					// Copy the image path
					$view_parameters['image_path'] = sprintf('/image/get/%s', $guid);
					// Show the delete confirmation form
					$view_parameters['confirm_delete'] = true;
					// Copy the GUID
					$view_parameters['image_guid'] = $guid;
					// Copy the deletion key
					$view_parameters['image_delete_key'] = $key;

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
		Description: Marks an image or a set of images as deleted on the server.

		@param TO DO
		@returns TO DO
	*/
	public function deleteImage($guid, $key) {
		return '';
	}
}
