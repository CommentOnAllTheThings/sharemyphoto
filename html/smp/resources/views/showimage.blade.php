@extends('layouts.master')
@section('pagetitle', 'Image')
@section('content')
    <div class="container">
        <div class="text-center">
			<h2>{{ $image_title }}</h2>
			<img src="{{ $image_path }}" alt="{{ $image_title }}" class="img-responsive center-block" />
		</div>
	</div>
@stop