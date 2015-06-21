@extends('layouts.master')
@section('pagetitle', 'Gallery')
@section('headerincludes')
<link href="/smp/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/smp/delete.js"></script>
@stop
@section('content')
	<div class="container">
		<div class="text-center">
			@if (session('deletedmessage'))
				@if (session('deletedmessagetype') === 1)
					<div class="alert alert-success text-left" id="info-success">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<strong>{{ session('deletedmessage') }}</strong>
					</div>
				@elseif (session('deletedmessagetype') === 2)
					<div class="alert alert-danger text-left" id="info-danger">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<strong>{{ session('deletedmessage') }}</strong>
					</div>
				@else
					<div class="alert alert-info text-left" id="info-info">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<strong>{{ session('deletedmessage') }}</strong>
					</div>
				@endif
			@endif
			@if ($no_images)
				<h2>No Images have been uploaded to the site!</h2>
			@else
				<!-- Images -->
				<form method="POST" onsubmit="return validateDeletionForm(false);" id="mass-deletion" action="/image/delete">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<!-- Iterate through the list of images and create the thumbnails and links -->
								@foreach ($list_images as $image)
									<div class="col-xs-4 col-sm-3 col-md-2 img-padding">
										<a href="/image/view/{{ $image->image_guid }}"><img src="/image/get/thumbnail/{{ $image->image_guid }}" alt="{{ $image->image_title }}" class="img-responsive center-block"></a>
										<!-- Gallery Image Deletion -->
										<div class="checkbox gallery-delete-hide">
											<label>
												<input type="checkbox" name="{{ $image->image_guid }}" value="1"> Delete this Image?
											</label>
										</div>
									</div>
								@endforeach
							</div>
						</div>
					</div>
					<!-- Delete Button -->
					<div class="text-center">
						<p class="validation-error gallery-delete-hide" id="image-deletion-validation"></p>
						<button type="submit" class="btn btn-danger gallery-delete-hide" id="delete-button">Delete</button>
						<button type="submit" onclick="return false;" class="btn btn-success gallery-delete-hide" id="trigger-cancel-button">Cancel</button>
						{!! csrf_field() !!}
					</div>
				</form>
				<!-- Deletion Tool Trigger Button -->
				<button type="submit" class="btn btn-default" id="trigger-delete-button"><span class="glyphicon glyphicon-remove"></span> Show Deletion Tool</button>
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
			@endif
		</div>
	</div>
@stop