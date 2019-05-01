<?php
namespace app\controllers;

use core\Controller;

class MainController extends Controller {
	
	private $obj;
	
	public function indexAction($params) {
		$this->view->render('A great way to organize your work | Quemer');
	}
	
	public function featuresAction($params) {
		$this->view->render('Features | Quemer');
	}
	
	public function pricingAction($params) {
		$this->view->render('Pricing | Quemer');
	}
	
}

?>