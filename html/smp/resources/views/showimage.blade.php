@extends('layouts.master')
@section('pagetitle', 'Image')
@section('content')
    <div class="container">

        <div class="text-center">
			@if ($confirm_delete === true)
				<h2>Are you sure you want to delete the following image?</h2>
			@endif
			<h2>
				{{ $image_title }}
			</h2>
			<img src="{{ $image_path }}" alt="{{ $image_title }}" class="img-responsive center-block" />
		</div>
		@if($confirm_delete === true)
			<br/>
			<form id="delete-form" method="POST" action="/image/delete/{{ $image_guid }}/{{ $image_delete_key }}">
				<!-- Notification -->
				@if (session('message'))
					<div class="alert alert-danger" id="info-danger">
				        <strong>{{ session('message') }}</strong>
				    </div>
				@endif

				<!-- Buttons -->
				<div class="text-center">
					<button type="submit" class="btn btn-danger" id="delete-button">Yes</button>
					<a href="/image/view/{{ $image_guid }}" class="btn btn-success" role="button">No</a>
					{!! csrf_field() !!}
				</div>
			</form>
		@endif
	</div>
@stop