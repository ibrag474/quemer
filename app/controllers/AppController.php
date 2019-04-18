<?php

namespace app\controllers;

use core\Controller;

class AppController extends Controller {
	
	private $obj;
	
	public function showAction($params) {
		$this->obj = $this->getRequest();
		if (!empty($this->obj)) {
			
		} else {
			/*$result = $this->model->getNotes();
			$vars = [
			'notes' => $result,
			];*/
			$this->view->layout = 'app';
			$this->view->render('Quemer Web App');
		}
	}
	
	public function peopleAction($params) {
		$this->obj = $this->getRequest();
		if (!empty($this->obj)) {
			if ($this->obj['act'] === 'searchPeople') {
				$this->model->searchPeople($this->obj);
			} else if ($this->obj['act'] === 'loadPeople') {
				$this->model->loadPeople($this->obj);
			} else if ($this->obj['act'] === 'managePeople') {
				$this->model->managePeople($this->obj);
			} else if ($this->obj['act'] === 'invitePeople') {
				$this->model->invitePeople($this->obj);
			}
		} else {
			$this->view->layout = 'app';
			$this->view->render('People | Quemer Web App');
		}
	}
	
	public function profileAction($params) {
		$this->obj = $this->getRequest();
		if (!empty($this->obj)) {
			/*if ($this->obj['act'] === 'getProfile') {
				$accModel->getProfile();
				//$this->model->getProfile($this->obj);
			}*/
		} else {
			$this->view->layout = 'app';
			$this->view->render('Profile | Quemer Web App');
		}
	}
	
}

?>