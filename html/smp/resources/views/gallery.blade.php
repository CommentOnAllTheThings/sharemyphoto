@extends('layouts.master')
@section('pagetitle', 'Gallery')
@section('headerincludes')
<link href="/smp/style.css" rel="stylesheet" type="text/css">
@stop
@section('content')
	<div class="container">
		<div class="text-center">
			@if ($no_images)
				<h2>No Images have been uploaded to the site!</h2>
			@else
				<!-- Images -->
				<div class="row">
					@foreach ($list_images as $image)
						<div class="col-xs-4 col-sm-3 col-md-2 img-padding">
							<a href="/image/view/{{ $image->image_guid }}"><img src="/image/get/thumbnail/{{ $image->image_guid }}" alt="{{ $image->image_title }}" class="img-responsive center-block"></a>
						</div>
					@endforeach
				</div>
				<nav>
					<ul class="pagination pagination-lg">
						<!-- << First -->
						@if ($current_page === 1)
							<li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
						@else
							<li><a href="/" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
						@endif

						@for ($page_counter = $min_page; $page_counter <= $max_page; $page_counter++)
							@if ($page_counter === $current_page)
								@if ($current_page === 1)
									<li class="active"><a href="/">1<span class="sr-only">(current)</span></a></li>
								@else
									<li class="active"><a href="/gallery/page/{{ $current_page }}">{{ $page_counter }}<span class="sr-only">(current)</span></a></li>
								@endif
							@else
								@if ($page_counter === 1)
									<li><a href="/">1</a></li>
								@else
									<li><a href="/gallery/page/{{ $page_counter }}">{{ $page_counter }}</a></li>
								@endif
							@endif
						@endfor

						<!-- Last >> -->
						@if ($current_page === $highest_page)
							<li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
						@else
							@if ($highest_page > 1)
								<li><a href="/gallery/page/{{ $highest_page }}" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
							@else
								<li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
							@endif
						@endif
					</ul>
				</nav>
				<!-- Page {{ $current_page }} of {{ $highest_page }}<br/> -->
				<!--@if($images_uploaded === 1)
					<h5>Site is hosting {{ $images_uploaded }} Image</h5>
				@else
					<h5>Site is hosting {{ $images_uploaded }} Images</h5>
				@endif-->
			@endif
		</div>
	</div>
@stop