@extends('layouts.master')
@section('pagetitle', 'Gallery')
@section('content')
	<div class="container">
		<div class="text-center">
			Gallery<br/>
			Page {{ $current_page }} of {{ $highest_page }}
		</div>
	</div>
@stop