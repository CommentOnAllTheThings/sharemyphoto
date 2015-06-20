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
		<h2 class="text-center">Upload Image</h2>
		<form id="upload-form" onsubmit="return uploadImage();" method="POST" action="/image/upload/save" enctype="multipart/form-data">
			<!-- Notification -->
			@if (session('message'))
				<div class="alert alert-danger" id="info-danger">
			        <strong>{{ session('message') }}</strong>
			    </div>
			@endif
			<div class="alert alert-info" id="info-notification" role="alert" style="display: none;"></div>

			<!-- Image Title -->
			<div id="upload-title-group" class="form-group">
				<label class="control-label" for="upload-title">Image Title*</label>
				<input type="text" id="upload-title" name="upload-title" class="form-control" placeholder="Title">
				<p id="upload-title-validation" class="help-block"></p>
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
				<p id="upload-file-validation" class="help-block"></p>
			</div>

			<div class="progress upload-bar-container upload-bar" id="upload-progress">
				<div class="progress-bar progress-bar-striped active" id="upload-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
					<span id="upload-file-progress-text">0%</span>
				</div>
			</div>

			<!-- Upload Button -->
			<div class="text-center">
				<button type="submit" class="btn btn-success" id="upload-button">Upload</button>
				{!! csrf_field() !!}
			</div>
		</form>
	</div>
@stop