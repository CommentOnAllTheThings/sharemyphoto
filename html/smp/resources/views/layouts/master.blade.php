<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Page Title -->
        <title>ShareMyPho.to - @yield('pagetitle')</title>

        <!-- CSRF -->

        <!-- Load jQuery -->
        <script type="text/javascript" src="/thirdparty/jquery/jquery.min.js"></script>

        <!-- Load Bootstrap -->
        <link href="/thirdparty/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="/thirdparty/bootstrap/js/bootstrap.min.js"></script>    

        @section('headerincludes')
        @show
    </head>
<body>
    @section('bodyincludes')
    @show
	<!-- Page Header -->
    @section('header')
        <nav class="navbar navbar-default">
        	<div class="container-fluid">
        		<div class="navbar-header">
        			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu-collapse" aria-expanded="false">
        				<span class="sr-only">Toggle Navigation</span>
        				<span class="icon-bar"></span>
        				<span class="icon-bar"></span>
        				<span class="icon-bar"></span>
    				</button>
    				<a class="navbar-brand" href="/">ShareMyPho.to</a>
        		</div>

        		<div class="collapse navbar-collapse" id="main-menu-collapse">
        			<ul class="nav navbar-nav">
        				<li><a href="/">Gallery</a></li>
        				<li><a href="/image/upload">Upload</a></li>
        			</ul>
        		</div>
        	</div>
        </nav>
    @show

    <!-- Page Content -->
    @section('content')
    	<div class="container">
    		<div class="text-center">
    			<!-- Main Content -->
    		</div>
    	</div>
    @show

	<!-- Page Footer -->
	@section('footer')
		<div class="text-center">
			<!-- Copyright -->
			<h5>Copyright &copy; 2015 ShareMyPho.to, All Rights Reserved.</h5>
			
			<!-- Disclaimer -->
			<h6>ShareMyPho.to is not responsible for the content of any images uploaded to the site.</h6>
		</div>
	@show
</body>
</html>