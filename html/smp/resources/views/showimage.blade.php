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
			@if ($confirm_delete === false && strlen($image_description) > 0)
				<br/>
				<div class="well">{{ $image_description }}</div>
			@endif
		</div>
		@if($confirm_delete === true)
			<br/>
			<form id="delete-form" method="POST" action="/image/delete/{{ $image_guid }}/{{ $image_delete_key }}">
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