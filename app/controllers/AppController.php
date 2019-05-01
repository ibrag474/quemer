<?php

namespace app\controllers;

use core\Controller;

class AppController extends Controller {
	
	private $obj;
	
	public function showAction($params) {
		$this->view->layout = 'app';
		$this->view->render('Quemer Web App');
	}
	
	public function peopleAction($params) {
		$this->view->layout = 'app';
		$this->view->render('People | Quemer Web App');
	}
	
	public function profileAction($params) {
		$this->view->layout = 'app';
		$this->view->render('Profile | Quemer Web App');
	}
	
}

?>