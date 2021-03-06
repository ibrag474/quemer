<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title; ?></title>
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-130099814-1"></script>
	<script>
 		 window.dataLayer = window.dataLayer || [];
  		function gtag(){dataLayer.push(arguments);}
  		gtag('js', new Date());

  		gtag('config', 'UA-130099814-1');
	</script>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#32d8ca">
	<script crossorigin src="https://unpkg.com/react@16/umd/react.production.min.js"></script>
	<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.production.min.js"></script>
	<link rel="stylesheet" href="/app/views/design/account.css">
	<script type="text/javascript" src="/app/js/account.js"></script>
	<script type="text/javascript" src="/app/js/ajax.js"></script>
</head>
<body>
	<?php echo $content; ?>
</body>
</html>