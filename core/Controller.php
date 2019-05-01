<?php

namespace core;

abstract class Controller {
	
	public $route;
	public $view;
	public $groups;
	
	public function __construct($route) {
		$this->route = $route;
		$this->view = new View($route);
	}
}

?>