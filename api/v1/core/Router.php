<?php

namespace core;

class Router {
	
	protected $routes = [];
	protected $url = array();
	protected $controller;
	
	public function __construct() {
		$arr = require 'config/routes.php';
		foreach($arr as $key => $val) {
			$this->routes[$key] = $val;
		}
	}
	
	public function match() {
		$this->url = trim($_SERVER['REQUEST_URI'], '/');
		$pattern = '([^/?]+)';
		preg_match_all($pattern, $this->url, $matches);
		$this->url = $matches[0];
		if (count($this->url) > 2) {
			if (array_key_exists($this->url[2], $this->routes))
				return true;
		}
		return false;
	}
	
	public function callFunc($req, $class, $func) {
		$function = $req . $func;
		if (method_exists($class, $function)) {
			$this->controller->$function();
		}
	}
	
	public function run() {
		if ($this->match()) {
			$path = 'controllers\\' . $this->routes[$this->url[2]] . 'Controller';
			if (class_exists($path)) {
				$this->controller = new $path($this->url);
				$this->controller->run();
				switch($_SERVER['REQUEST_METHOD']) {
					case 'GET' :
						$urlLength = count($this->url);
						if ($urlLength == 3) $this->controller->loadAll();
						else if ($urlLength >= 4) $this->callFunc("load" , $path, $this->url[3]);
						break;
					case 'POST' :
						$urlLength = count($this->url);
						if ($urlLength >= 4) $this->callFunc("send" , $path, $this->url[3]);
						break;
					case 'PUT' :
						$urlLength = count($this->url);
						if ($urlLength >= 4) $this->callFunc("edit" , $path, $this->url[3]);
						break;
					case 'DELETE' :
						$urlLength = count($this->url);
						if ($urlLength >= 4) $this->callFunc("delete" , $path, $this->url[3]);
						break;
				}
			} else http_response_code(404);
		} else http_response_code(404);
	}
 
}

?>