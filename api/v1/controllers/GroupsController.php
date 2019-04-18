<?php

namespace controllers;

use core\Controller;

class GroupsController extends Controller {
	
	protected $userData;
	
	public function run() {
		$this->userData = $this->validateJWT();
		if ($this->userData == false) exit();
	}
	
	//GET
	public function loadAll() {
		try {
			$data = $this->model->loadAll(array("userid" => $this->userData->data->id));
			$this->sendJSON($data);
		} catch (\Exception $e) {
			$this->error(404, array(
				"message" => "Unable to load all groups.",
				"exception" => $e->getMessage()
			));
		}
	}
	
	public function loadGroup() {
		$params = $this->getJSON();
		if (array_key_exists("groupid", $params)) {
			try {
				$data = $this->model->loadGroup(array("userid" => $this->userData->data->id, "groupid" => $params["groupid"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to load group.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	//POST
	public function sendGroup() {
		$params = $this->getJSON();
		if (array_key_exists('title', $params) && array_key_exists('content', $params)) {
			try {
				$data = $this->model->createGroup(array("userid" => $this->userData->data->id, "title" => $params["title"], "content" => $params["content"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to create group.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	//PUT
	public function editGroup() {
		$params = $this->getJSON();
		if (array_key_exists('title', $params) && array_key_exists('content', $params) && array_key_exists('groupid', $params)) {
			try {
				$data = $this->model->editGroup(array("userid" => $this->userData->data->id, "title" => $params["title"], "groupid" => $params["groupid"], "content" => $params["content"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to edit group.",
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
	public function deleteGroup() {
		$params = $this->getJSON();
		if (array_key_exists('groupid', $params)) {
			try {
				$this->model->deleteGroup(array("userid" => $this->userData->data->id, "groupid" => $params["groupid"]));
				$this->sendJSON(array("message" => "Group is deleted successfully."));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to delete group.",
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

?>