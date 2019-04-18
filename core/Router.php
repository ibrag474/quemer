<?php

namespace core;

class Router {
	
	protected $url;
	protected $dividedUrl;
	protected $foundParams = false;
	protected $routes = [];
    protected $params = [];
	
	public function __construct() {
		$arr = require 'app/config/routes.php';
		foreach ($arr as $key => $val) {
            $this->add($key, $val);
        }
	}
	
	public function add($route, $params) {
		$route = '#^'.$route.'$#';
		$this->routes[$route] = $params;
	}
	
	public function match() {
		$this->url = trim($_SERVER['REQUEST_URI'], '/');
		$this->dividedUrl = explode('/', $this->url);
		if (count($this->dividedUrl) > 2 && strlen($this->dividedUrl[2]) > 0) {
			$this->url = $this->dividedUrl[0] . '/' . $this->dividedUrl[1];
			$this->foundParams = true;
		}
		foreach ($this->routes as $route => $params) {
			if (preg_match($route, $this->url, $match)) {
				$this->params = $params;
				return true;
			}
		}
		return false;
	}
	
	public function run() {
		if ($this->match()) {
			$path = 'app\controllers\\'.ucfirst($this->params['controller']).'Controller';
			if (class_exists($path)) {
				$action = $this->params['action'].'Action';
				if (method_exists($path, $action)) {
					$controller = new $path($this->params);
					if ($this->foundParams == true)
					$controller->$action($this->dividedUrl[2]);
					else $controller->$action(0);
				} else {
                    View::errorCode(404);
                }
            } else {
                View::errorCode(404);
            }
        } else {
			View::errorCode(404);
        }
    }
}

?>