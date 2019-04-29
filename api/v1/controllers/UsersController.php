<?php

namespace controllers;

use libs\jwt\JWT;
use core\Controller;

class UsersController extends Controller {
	
	protected $userData;
	
	public function run() {
		$this->userData = $this->validateJWT();
		if ($this->userData == false) exit();
	}
	
	//GET
	public function loadAll() {
		//nothing to do
	}
	
	public function loadMe() {
		try {
			$data = $this->model->loadMe(array("userid" => $this->userData->data->id));
			$this->sendJSON($data);
		} catch (\Exception $e) {
			$this->error(400, array(
				"message" => "Unable to load user profile.",
				"exception" => $e->getMessage()
			));
		}
	} 
		
	//POST
	
	//PUT
	public function editDetails() {
		$json = $this->getJSON();
		if (array_key_exists("surname", $json)) {
			try {
				$data = $this->model->editAccDetails(array("userid" => $this->userData->data->id, "surname" => $json['surname']));
				$this->sendJSON(array("message" => "Account details changed succefully."));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to change account details.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	//DELETE
	
}

?>