@extends('layouts.master')
@section('pagetitle', 'Upload Image')
@section('headerincludes')
<link href="/smp/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/smp/upload.js"></script>
<script type="text/javascript">
	// Add the CSRF Token
	csrf_token = "{{ csrf_token() }}";
</script>
@stop
@section('content')
	<div class="container">
		<form id="upload-form" onsubmit="return uploadImage();" action="#">
			<!-- Image Title -->
			<div id="upload-title-group" class="form-group">
				<label class="control-label" for="upload-title">Image Title*</label>
				<input type="text" id="upload-title" name="upload-title" class="form-control" placeholder="Title">
				<p id="upload-title-validation" class="validation-error"></p>
			</div>

			<!-- Image Description -->
			<div id="upload-description-group" class="form-group">
				<label class="control-label" for="upload-description">Image Description</label>
				<textarea id="upload-description" name="upload-description" class="form-control" placeholder="Description" rows="3"></textarea>
			</div>

			<!-- Image File -->
			<div id="upload-file-group" class="form-group">
				<label class="control-label" for="upload-file">Image File*</label>
				<input type="file" id="upload-file" name="upload-file" accept=".jpg,.jpeg,.bmp,.gif,.png">
				<p id="upload-file-validation" class="validation-error"></p>
			</div>

			<!-- Upload Button -->
			<button type="submit" class="btn btn-success" id="upload-button">Upload</button>
		</form>
	</div>
@stop