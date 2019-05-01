<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title; ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#32d8ca">
	<link rel="stylesheet" href="/app/js/lib/bootstrap411/css/bootstrap.min.css">
	<link rel="stylesheet" href="/app/views/design/app.css">
	<link rel="stylesheet" href="/app/views/design/mediaQueries.css">
	<script crossorigin src="https://unpkg.com/react@16/umd/react.production.min.js"></script>
	<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.production.min.js"></script>
	<script type="text/javascript" src="/app/js/app/appUI.js"></script>
	<!-- new scripts -->
	<script type="text/javascript" src="/app/js/ajax.js"></script>
	<!-- end of new scripts -->
	<script src="/app/js/lib/jquery-3.3.1.slim.min.js"></script>
	<script src="/app/js/lib/popper.min.js"></script>
	<script src="/app/js/lib/bootstrap411/js/bootstrap.min.js"></script>
</head>
<body>

	<div class="header">
		
	</div>
	<div class="underHeader"></div>
	
	<?php echo $content; ?>
	<script type="text/javascript" src="/app/js/app/onloads.js"></script>
</body>
</html>