@extends('layouts.master')
@section('pagetitle', 'Image')
@section('content')
    <div class="container">
        <div class="text-center">
			<img src="{{ $image_path }}" class="img-responsive center-block" />
		</div>
	</div>
@stop