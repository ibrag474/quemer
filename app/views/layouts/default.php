<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title; ?></title>
	<meta charset="UTF-8">
	<meta name="description=" content="Quemer.com is the web app that allows you to easily create group tasks and notes for free. It is an early access app, so there will be more features in the future.">
	<meta name="google-site-verification" content="Br1k-rAMvH2gSwAo1SZ5B8P4E1JADyHuQM6K-PR9la8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta property="og:image" content="https://quemer.com/views/design/images/home/ogImage.png">
	<meta name="theme-color" content="#32d8ca">
	<link rel="stylesheet" href="/app/js/lib/bootstrap411/css/bootstrap.min.css">
	<link rel="stylesheet" href="/app/views/design/default.css">
	<script src="/app/js/lib/jquery-3.3.1.slim.min.js"></script>
	<script src="/app/js/lib/popper.min.js"></script>
	<script src="/app/js/lib/bootstrap411/js/bootstrap.min.js"></script>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light">
		<div class="container">
			<a class="navbar-brand" href="/"><img src="/app/views/design/images/home/QUEMERlogo.png" height="30px"></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="/main/features">Features</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="/main/pricing">Pricing</a>
					</li>
				</ul>
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link" href="/account/login">Login</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="/account/register">Sign up</a>
					</li>
				</ul> 
			</div>
		</div>
	</nav>
	<?php echo $content; ?>
	
	<div class="footer">
		<br>
		<div class="container">
			<div class="row">
				<div class="col-md-5"><img src="/app/views/design/images/home/Logo.png" width="50px" height="54px"></div>
				<div class="col-md-2">
					<ul style="list-style-type:none">
						<li><h4>PRODUCT</h4></li>
						<li><a href="/main/features">Features</a></li>
						<li><a href="/main/pricing">Pricing</a></li>
					</ul>
				</div>
				<div class="col-md-2">
					<ul style="list-style-type:none">
						<li><h4>SOCIAL MEDIA</h4></li>
						<li><a target="_blank" href="https://www.facebook.com/batonstu/">Facebook</a></li>
					</ul>
				</div>
				<div class="col-md-2">
					<ul style="list-style-type:none">
						<li><h4>LEGAL & SECURITY</h4></li>
						<li><a href="/app/views/legal/qtou.pdf">Terms Of Use</a></li>
						<li><a href="/app/views/legal/qpp.pdf">Privacy Policy</a></li>
					</ul>
				</div>
			</div>
			<p style= "margin-top: 50px;">© 2018-2019 Quemer.com. All rights reserved.</p>
		</div>
	</div>
</body>
</html>
