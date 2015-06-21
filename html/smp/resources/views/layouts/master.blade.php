<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Page Title -->
        <title>ShareMyPho.to - @yield('pagetitle', 'Welcome')</title>

        <!-- Viewport Meta Tag for Mobile Devices -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

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
        <!-- Navigation -->
        <nav class="navbar navbar-default">
        	<div class="container-fluid">
        		<div class="navbar-header">
                    <!-- Responsive 3 bar button on mobile devices -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu-collapse" aria-expanded="false">
        				<span class="sr-only">Toggle Navigation</span>
        				<span class="icon-bar"></span>
        				<span class="icon-bar"></span>
        				<span class="icon-bar"></span>
    				</button>

                    <!-- ShareMyPho.to Home Page link -->
    				<a class="navbar-brand" href="/">ShareMyPho.to</a>
        		</div>

                <!-- Navigation Bar Items -->
        		<div class="collapse navbar-collapse" id="main-menu-collapse">
                    <!-- Gallery and Upload links -->
        			<ul class="nav navbar-nav">
                        <li><a href="/"><span class="glyphicon glyphicon-home"></span> Gallery</a></li>
                        <li><a href="/image/upload"><span class="glyphicon glyphicon-cloud-upload"> Upload</a></li>
        			</ul>

                    <!-- GitHub Link -->
                    <ul class="nav navbar-nav navbar-right">
                        <a href="https://github.com/CommentOnAllTheThings/sharemyphoto" role="button" class="btn btn-info navbar-btn navbar-btn-margin-left" target="_blank">sharemyphoto Project on GitHub</a>
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
            <h6>All images uploaded to ShareMyPho.to are property of their respective copyright owners.</h6>
		</div>
	@show
</body>
</html>