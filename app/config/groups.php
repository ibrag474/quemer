<?php

return [
	'all' => [
		'main/index',
		'main/features',
		'main/pricing',
	],
	'authorized' => [
		'app/show',
		'app/people',
		'app/profile',
		'account/logout'
	],
	'guest' => [
		'account/register',
		'account/login',
		'account/activate',
		'account/restore'
	],
	'admin' => [
		//
	],
	
];
?>