<?php

namespace core;

abstract class Controller {
	
	public $route;
	public $view;
	public $groups;
	
	public function __construct($route) {
		$this->route = $route;
		/*if (!$this->checkGroups()) {
			View::errorCode(403);
		}*/
		$this->view = new View($route);
		$this->model = $this->loadModel($route['controller']);
	}

	public function loadModel($name) {
		$path = 'app\models\\'.ucfirst($name);
		if (class_exists($path)) {
			return new $path;
		}
	}
	
	public function checkGroups() {
		$this->groups = require_once 'app/config/groups.php';
		if ($this->isGroup('all')) {
			return true;
		} else if ($this->isGroup('authorized') and isset($_SESSION["logged_in"]) == 1) {
			return true;
		} else if ($this->isGroup('guest') and isset($_SESSION["logged_in"]) == 0) {
			return true;
		} else if ($this->isGroup('admin') and isset($_SESSION["logged_in"]) == 2) {
			return true;
		} 
		return false;
	}
	
	public function isGroup($key) {
		return in_array($this->route['controller'] . '/' . $this->route['action'], $this->groups[$key]);
	}
	
	public function getRequest() {
		$obj;
		if (isset($_GET["param"])) {
			$obj = json_decode($_GET["param"], true);
			$obj = filter_var_array($obj, FILTER_SANITIZE_STRING);
		} else $obj = null;
		return $obj;
	}
}

?>