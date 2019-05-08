<?php

namespace controllers;

use core\Controller;

class NotificationsController extends Controller {
	
	protected $userData;
	
	public function run() {
		$this->userData = $this->validateJWT();
		if ($this->userData == false) exit();
	}
	
	//GET
	public function loadAll() {
		$data = $this->model->loadAll();
		$this->sendJSON($data);
	}
	
}

?>