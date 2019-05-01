<?php

return [
	'' => [
		'controller' => 'main',
		'action' => 'index',
	],
	
	'main/features' => [
		'controller' => 'main',
		'action' => 'features',
	],
	
	'main/pricing' => [
		'controller' => 'main',
		'action' => 'pricing',
	],
	
	'account/login' => [
		'controller' => 'account',
		'action' => 'login',
	],
	
	'account/register' => [
		'controller' => 'account',
		'action' => 'register',
	],
	
	'account/restore' => [
		'controller' => 'account',
		'action' => 'restore',
	],
	
	'account/activation' => [
		'controller' => 'account',
		'action' => 'activation',
	],
	
	'account/logout' => [
		'controller' => 'account',
		'action' => 'logout',
	],
	
	'app/show' => [
		'controller' => 'app',
		'action' => 'show',
	],
	
	'app/people' => [
		'controller' => 'app',
		'action' => 'people',
	],
	
	'app/profile' => [
		'controller' => 'app',
		'action' => 'profile',
	]
];

?>