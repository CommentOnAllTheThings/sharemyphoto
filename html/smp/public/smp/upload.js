// Our CSRF token, we won't be able to upload anything without a CSRF token!
var csrf_token = null;

// Our flag to block multiple AJAX calls from being performed
var block_ajax = false;

// Our RegExp test for strings
var test_string = /^[A-Za-z0-9\s!@#\$%&\?]+$/i;

// The file extensions we will allow users to upload
var image_extensions = ['.jpg', '.jpeg', '.bmp', '.gif', '.png'];

// Initiate our bindings to buttons, fields etc. to perform validation as the user changes things in the form
$(document).ready(function(){
	// Add binding for title field
	$('#upload-title').on('keyup blur', function(){
		validateTitle(true);
	});

	// Add binding for file field
	$('#upload-file').on('change blur', function(){
		validateFile(true);
	});

	/*$('#upload-button').on('click', function(){
		uploadImage();
	});*/
});

var image_upload_interval = null;
var image_upload_progress = 0;

function uploadImage() {
	// Check if the required information was entered into the form...
	var validation_ok = validateUpload();
	
	// We will only upload the image if everything checks out and we are not performing another upload at the same time!
	if (validation_ok) {
		// Show a message notifying the user we are uploading the image now...
		$('#info-notification').show().html('<strong>Uploading image...please wait.</strong>');

		// Show the upload progress bar
		$('#upload-progress').show();

		// Upload the image!

		// Create our AJAX call to poll the server to get the image upload progress
		// This will make a request every 5 seconds to update the progress
		if (image_upload_interval == null) {
			image_upload_interval = setInterval(function(){
				// Get a JSON progress response
				$.getJSON('/image/upload/progress').done(function(data) {
					// Iterate through the response
					$.each(data, function(key, value) {
						// Get the progress
						if (key == 'progress') {
							// Convert the percentage to an integer
							var percentage = parseInt(value);

							// Update the progress bar
							updateUploadProgress(percentage);

							// Are we done?
							if (typeof progress !== 'undefined' && percentage == 100) {
								// Tell the user we're done and clear this interval
								clearInterval(image_upload_interval);
								image_upload_interval = null;
								$('#info-notification').html('<strong>Image upload complete.</strong>');
							}
						}
					});
				});
			}, 5000);
		}
		return true;
	}

	return false;
}

/*function updateUploadProgress(progress) {
	// Default progress is 0%
	var display_progress = 0;

	// Check if we can update the progress, we can't do anything if percentage isn't a number!
	// Once that's done, see how far we've progressed...
	if (typeof progress !== 'undefined' && progress != 'NaN' && progress >= 0 && progress <= 100)
		display_progress = progress;

	// Width Attribute - upload_progress_percentage CONCAT %
	var upload_progress = display_progress + '%';

	$('#upload-progress-bar').css('width', upload_progress);
	$('#upload-progress-bar').attr('aria-valuenow', display_progress);

	$('#upload-file-progress-text').text(upload_progress);
}*/

// Validates the title entered
function validateTitle(suppress) {
	// Make sure suppress is set to either true or false
	suppress = suppress ? true : false;

	var title_value = $('#upload-title').val();
	if (title_value != null && title_value.length > 0) {
		// Test our string against our RegExp test for strings
		var title_valid = test_string.test(title_value);

		// Check to see if it is valid or not
		if (title_valid) {
			// Ok
			if (title_value.length > 1) {
				// Remove the error class if it still has it applied
				if ($('#upload-title-group').hasClass('has-error')) {
					$('#upload-title-group').removeClass('has-error');
				}

				// Clear the validation error message just in case!
				$('#upload-title-validation').text('');
				return true;
			}
			else {
				if (!suppress) {
					// Error - Title is too short
					// Add the error class if it has not already been applied
					if (!$('#upload-title-group').hasClass('has-error')) {
						$('#upload-title-group').addClass('has-error');
					}

					// Show an error message
					$('#upload-title-validation').text('Image titles must be a combination of any two or more uppercase and/or lowercase letters A to Z, and/or numbers, and/or spaces.');
				}
			}
		}
		else {
			if (!suppress) {
				// Error - Invalid characters in the title
				// Add the error class if it has not already been applied
				if (!$('#upload-title-group').hasClass('has-error')) {
					$('#upload-title-group').addClass('has-error');
				}

				// Show an error message
				$('#upload-title-validation').text('Image titles can only contain both uppercase and lowercase letters A to Z, numbers and spaces.');
			}
		}
	}
	else {
		// Error - Empty or null
		// Add the error class if it has not already been applied
		if (!suppress) {
			if (!$('#upload-title-group').hasClass('has-error')) {
				$('#upload-title-group').addClass('has-error');
			}

			// Show an error message
			$('#upload-title-validation').text('Please enter a suitable title for the image.');
		}
	}
	return false;
}

// Validates the description entered
function validateDescription(suppress) {
	// This field is optional!
	// In reality it is pointless to validate it since the user could practically put anything here.
	// It makes no sense to block the user from uploading an image just because they decided to not fill it out, or filled it out but they enter weird characters and stuff.
	// So I will not bother validating it at all. We will just sanitize it server side.
	return true;
}

// Validates the file chosen for upload
function validateFile(suppress) {
	// Make sure suppress is set to either true or false
	suppress = suppress ? true : false;

	var file_name = $('#upload-file').val();

	if (file_name != null && file_name.length > 0) {
		// Check the file name against the list of extensions
		if (image_extensions != null && Array.isArray(image_extensions)) {
			var list_of_extensions = '';

			// Iterate through the list of extensions
			for (var i = 0; i < image_extensions.length; i++) {
				// Get the extension
				var check_extension = image_extensions[i];

				// Now see if the file name is longer than the extension.
				// It doesn't make sense to check it if it happens to be shorter...
				if (check_extension != null && file_name.length > check_extension.length) {
					// Grab the extension from the file name
					var file_compare_extension = file_name.substr(file_name.length - check_extension.length);
					if (file_compare_extension != null && check_extension.toLowerCase() == file_compare_extension.toLowerCase()) {
						// File name ok
						// We will check to see if it's actually an image file on the server side, so client side checks out!

						// Remmove the error class if it still has it applied
						if ($('#upload-file-group').hasClass('has-error')) {
							$('#upload-file-group').removeClass('has-error');
						}

						// Clear the validation error message just in case!
						$('#upload-file-validation').text('');

						return true;
					}
				}

				// Check the extension and append it to the list if it isn't null and length > 0
				if (check_extension != null && check_extension.length > 0) {
					// Append a comma in front of the extension if another extension precedes this one!
					if (list_of_extensions.length > 0)
						list_of_extensions += ', ';

					// Add the extension
					list_of_extensions += check_extension;
				}
			}

			// Add the error class if it has not already been applied
			if (!$('#upload-file-group').hasClass('has-error')) {
				$('#upload-file-group').addClass('has-error');
			}

			// Show an error message
			$('#upload-file-validation').text('You can only upload images with the following extension(s): ' + list_of_extensions);
		}
	}
	else {
		if (!suppress) {
			// Add the error class if it has not already been applied
			if (!$('#upload-file-group').hasClass('has-error')) {
				$('#upload-file-group').addClass('has-error');
			}

			// Show an error message
			$('#upload-file-validation').text('Please choose an image file to upload.');
		}
	}

	return false;
}

// Validates the upload form to ensure the title and file are both filled in
function validateUpload() {
	// Our temporary variable to keep track of the validation status at each step
	var validation_tmp = false;

	// Our variable that if even one condition does not pass, validation should not pass!
	var validation_passed = true;

	// Validate the title first
	validation_tmp = validateTitle(false);
	if (!validation_tmp) {
		// Title failed validation
		validation_passed = false;
	}

	// Validate the description (this really doesn't do anything but I leave it here for the future)
	validation_tmp = validateTitle(false);
	if (!validation_tmp) {
		// Description failed validation (How? Who knows!)
		validation_passed = false;
	}

	// Validate the file chosen
	validation_tmp = validateFile(false);
	if (!validation_tmp) {
		// File failed validation
		validation_passed = false;
	}

	// Send back the results of our checks
	return validation_passed;
}