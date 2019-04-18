<?php

namespace controllers;

use core\Controller;

class PeopleController extends Controller {
	
	protected $userData;
	
	public function run() {
		$this->userData = $this->validateJWT();
		if ($this->userData == false) exit();
	}
	// GET
	public function loadKnown() {
		$data = $this->model->known(array("userid" => $this->userData->data->id));
		if ($data) {
			$this->sendJSON($data);
		} else {
			$this->error(404, array(
				"message" => "Known people are not found."
			));
		}
	}
	
	public function loadFind() {
		$params = $this->getJSON();
		if ($params !== false && array_key_exists("name", $params)) {
			$data = $this->model->search(array("userid" => $this->userData->data->id, "name" => $params["name"]));
			if ($data) {
				$this->sendJSON($data);
			} else {
				$this->error(404, array(
					"message" => "We did not find anyone."
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	// POST
	public function sendInvite() {
		$params = $this->getJSON();
		if ($params !== false && array_key_exists("id", $params)) {
			try {
				$data = $this->model->invite(array("userid" => $this->userData->data->id, "id" => $params["id"]));
				$this->sendJSON(array("message" => "User was invited successfully."));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "User was not invited.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	// PUT
	public function editInvite() {
		$params = $this->getJSON();
		if ($params !== false && array_key_exists("id", $params)) {
			try {
				$this->model->acceptInvitation(array("userid" => $this->userData->data->id, "id" => $params["id"]));
				$this->sendJSON(array("message" => "Invitation is accepted successfully."));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Invitation is not accepted.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	// DELETE
	public function deleteInvite() {
		//delete known or invite
		$params = $this->getJSON();
		if ($params !== false && array_key_exists("id", $params)) {
			try {
				$data = $this->model->deleteKnown(array("userid" => $this->userData->data->id, "id" => $params["id"]));
				$this->sendJSON(array("message" => "Deletion performed successfully."));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Deletion was not successful.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
}