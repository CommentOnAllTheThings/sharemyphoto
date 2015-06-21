$(document).ready(function(){
	// Add binding for triggering delete functionality on the gallery page
	$('#trigger-delete-button').on('click', function(e){
		// Hide this button
		$(this).hide();

		// Show all hidden delete components
		$('.gallery-delete-hide').removeClass('gallery-delete-hide');
	});

	// Add binding for cancelling
	$('#trigger-cancel-button').on('click', function(e){
		// Hide all checkboxes, delete button, cancel button and validation message
		$('.checkbox, #delete-button, #image-deletion-validation, #trigger-cancel-button').addClass('gallery-delete-hide');

		// Show the "Show Deletion Tool" button
		$('#trigger-delete-button').show();

		// Clear all checkboxes
		$('input[type=checkbox]').removeAttr('checked');

		// Don't allow this button to trigger a post!
		e.preventDefault();
	});

	// Add binding to checkboxes to clear validation error message if we tried to submit after not checking anything
	$('input[type=checkbox]').on('change', function(e){
		validateDeletionForm(true);
	});
});

function validateDeletionForm(suppress) {
	// Determine if at least one image is marked for deletion
	var number_images_to_delete = 0;

	// Get the number of checkboxes checked
	number_images_to_delete = $('input[type=checkbox]:checked').length;

	// Check
	if (number_images_to_delete > 0) {
		// Ok, let's send the data to the server
		// Clear validation message
		$('#image-deletion-validation').text('');
		return true;
	}
	else {
		// Show an error message
		if (!suppress) {
			$('#image-deletion-validation').html('<strong>Please choose the image or images you would like to delete.</strong>');
		}
	}
	// Block
	return false;
}