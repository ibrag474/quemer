<?php
namespace app\controllers;

use core\Controller;

class MainController extends Controller {
	
	private $obj;
	
	public function indexAction($params) {
		$this->obj = $this->getRequest();
		if (!empty($this->obj)) {
			/*if ($this->obj['act'] === 'addNote') {
				$this->model->addNote($this->obj);
			}*/
		} else {
			$this->view->render('A great way to organize your work | Quemer');
		}
	}
	
	public function featuresAction($params) {
		$this->obj = $this->getRequest();
		if (!empty($this->obj)) {
			/*if ($this->obj['act'] === 'addNote') {
				$this->model->addNote($this->obj);
			}*/
		} else {
			$this->view->render('Features | Quemer');
		}
	}
	
	public function pricingAction($params) {
		$this->obj = $this->getRequest();
		if (!empty($this->obj)) {
			/*if ($this->obj['act'] === 'addNote') {
				$this->model->addNote($this->obj);
			}*/
		} else {
			$this->view->render('Pricing | Quemer');
		}
	}
	
}

?>