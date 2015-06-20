// Our CSRF token, we won't be able to upload anything without a CSRF token!
var csrf_token = null;

// Our flag to block multiple AJAX calls from being performed
var block_ajax = false;

// Our RegExp test for strings
var test_string = /[A-Za-z0-9 !#%&."',.\/\\?]/i;

// The file extensions we will allow users to upload
var image_extensions = ['.jpg', '.jpeg', '.bmp', '.gif', '.png'];

// Initiate our bindings to buttons, fields etc. to perform validation as the user changes things in the form
$(document).ready(function(){
	// TO DO
});

// 
function uploadImage() {
	// Check if the required information was entered into the form...
	var validation_ok = validateUpload();
	
	// We will only upload the image if everything checks out and we are not performing another upload at the same time!
	if (validation_ok && !block_ajax) {
		// Block any additional uploads while we are performing an upload!
		block_ajax = true;

		// Show the upload progress bar
		$('#upload-progress').show();

		// Upload the image!
		// TO DO
	}
}

// Validates the title entered
function validateTitle() {
	var title_value = $('#upload-title').val();
	if (title_value != null && title_value.length > 0) {
		// Test our string against our RegExp test for strings
		var title_valid = test_string.test(title_value);

		// Check to see if it is valid or not
		if (title_valid) {
			// Ok
			// Remmove the error class if it still has it applied
			if ($('#upload-title-group').hasClass('has-error')) {
				$('#upload-title-group').removeClass('has-error');
			}

			// Clear the validation error message just in case!
			$('#upload-title-validation').text('');
			return true;
		}
		else {
			// Error - Invalid characters in the title
			// Add the error class if it has not already been applied
			if (!$('#upload-title-group').hasClass('has-error')) {
				$('#upload-title-group').addClass('has-error');
			}

			// Show an error message
			$('#upload-title-validation').text('Image titles can only contain both uppercase and lowercase letters A to Z, numbers and spaces.');
		}
	}
	else {
		// Error - Empty or null
		// Add the error class if it has not already been applied
		if (!$('#upload-title-group').hasClass('has-error')) {
			$('#upload-title-group').addClass('has-error');
		}

		// Show an error message
		$('#upload-title-validation').text('Please enter a suitable title for the image.');
	}
	return false;
}

// Validates the description entered
function validateDescription() {
	// This field is optional!
	// In reality it is pointless to validate it since the user could practically put anything here.
	// It makes no sense to block the user from uploading an image just because they decided to not fill it out, or filled it out but they enter weird characters and stuff.
	// So I will not bother validating it at all. We will just sanitize it server side.
	return true;
}

// Validates the file chosen for upload
function validateFile() {
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
					var file_compare_extension = file_name.substr(file_name, -1 * (check_extension.length));
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
		// Add the error class if it has not already been applied
		if (!$('#upload-file-group').hasClass('has-error')) {
			$('#upload-file-group').addClass('has-error');
		}

		// Show an error message
		$('#upload-file-validation').text('Please choose an image file to upload.');
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
	validation_tmp = validateTitle();
	if (!validation_tmp) {
		// Title failed validation
		validation_passed = false;
	}

	// Validate the description (this really doesn't do anything but I leave it here for the future)
	validation_tmp = validateTitle();
	if (!validation_tmp) {
		// Description failed validation (How? Who knows!)
		validation_passed = false;
	}

	// Validate the file chosen
	validation_tmp = validateFile();
	if (!validation_tmp) {
		// File failed validation
		validation_passed = false;
	}

	// Send back the results of our checks
	return validation_passed;
}