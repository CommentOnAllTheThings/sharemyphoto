<!DOCTYPE html>
<html lang="en">
    <head>
    	<!-- Page Title -->
    	<title>ShareMyPho.to<?php
    		// Only append a dash and the title if the page title is set!
    		if (isset($page_title) && strlen($page_title) > 0) {
    			echo sprintf('- %s', htmlspecialchars($page_title, ENT_NOQUOTES));
    		}
    	?></title>

    	<!-- Load jQuery -->
        <script type="text/javascript" src="/thirdparty/jquery/jquery.min.js"></script>

        <!-- Load Bootstrap -->
        <link href='/thirdparty/bootstrap/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
        <script type="text/javascript" src="/thirdparty/bootstrap/js/bootstrap.min.js"></script>	
    </head>
    <body>
    	<!-- Page Content -->