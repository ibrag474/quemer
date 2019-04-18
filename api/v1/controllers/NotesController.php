<?php

namespace controllers;

use core\Controller;

class NotesController extends Controller {
	
	protected $userData;
	
	public function run() {
		$this->userData = $this->validateJWT();
		if ($this->userData == false) exit();
	}
	//GET
	public function loadAll() {
		//json isvesti perkelti i controller
			$data = $this->model->loadAll(array("userid" => $this->userData->data->id));
			$this->sendJSON($data);
	}
	
	public function loadNote() {
		$params = $this->getJSON();
		if (array_key_exists("id", $params)) {
			$note = $this->model->loadNote(array("userid" => $this->userData->data->id, "type" => 0), $params["id"]);
			if ($note !== false) {
				$this->sendJSON($note);
			} else $this->error(404, array(
				"message" => "The requested note was not found."
			));
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	public function loadTask() {
		$params = $this->getJSON();
		if (array_key_exists("id", $params)) {
			$task = $this->model->loadTask(array("userid" => $this->userData->data->id, "type" => 1), $params["id"]);
			if ($task !== false) {
				$this->sendJSON($task);
			} else $this->error(404, array(
				"message" => "The requested tasks was not found."
			));
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	public function loadSharedNote() {
		$params = $this->getJSON();
		if (array_key_exists("id", $params) && array_key_exists("groupid", $params)) {
			$data = $this->model->loadSharedNote(array("userid" => $this->userData->data->id, "id" => $params["id"], "groupid" => $params["groupid"]));
			if ($data !== false) {
				$this->sendJSON($data);
			} else $this->error(404, array(
				"message" => "The requested note were not found."
			));
		} else {
			$data = $this->model->loadSharedNotes(array("userid" => $this->userData->data->id));
			if ($data !== false) {
				$this->sendJSON($data);
			} else $this->error(404, array(
				"message" => "The requested notes were not found."
			));
		}
	}
	
	public function loadSharedTask() {
		$params = $this->getJSON();
		if (array_key_exists("id", $params) && array_key_exists("groupid", $params)) {
			$data = $this->model->loadSharedTask(array("userid" => $this->userData->data->id, "id" => $params["id"], "groupid" => $params["groupid"]));
			if ($data !== false) {
				$this->sendJSON($data);
			} else $this->error(404, array(
				"message" => "The requested tasks were not found."
			));
		} else {
			$data = $this->model->loadSharedTasks(array("userid" => $this->userData->data->id));
			if ($data !== false) {
				$this->sendJSON($data);
			} else $this->error(404, array(
				"message" => "The requested tasks were not found."
			));
		}
	}
	
	//POST
	public function sendNote() {
		$params = $this->getJSON();
		if (array_key_exists("title", $params) && array_key_exists("content", $params) && array_key_exists("shareTo", $params)) {
			try {
				$data = $this->model->saveNote(array("userid" => $this->userData->data->id, "title" => $params["title"], "content" => $params["content"], "shareTo" => $params["shareTo"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to add note.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	public function sendTask() {
		$params = $this->getJSON();
		if (array_key_exists("title", $params) && array_key_exists("content", $params) && array_key_exists("shareTo", $params)) {
			try {
				$data = $this->model->saveTask(array("userid" => $this->userData->data->id, "title" => $params["title"], "content" => $params["content"], "shareTo" => $params["shareTo"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to add note.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
		
	}
	
	public function sendShare() {
		$params = $this->getJSON();
		if (array_key_exists("groupName", $params) && array_key_exists("recID", $params)) {
			try {
				$data = $this->model->shareContent(array("userid" => $this->userData->data->id, "groupName" => $params["groupName"], "recID" => $params["recID"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to share content.",
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
	public function editNote() {
		$params = $this->getJSON();
		if (array_key_exists("title", $params) && array_key_exists("content", $params) && array_key_exists("individ", $params) && !array_key_exists("groupid", $params)) {
			try {
				$data = $this->model->editNote(array("userid" => $this->userData->data->id, "title" => $params["title"], "content" => $params["content"], "individ" => $params["individ"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to edit note.",
					"exception" => $e->getMessage()
				));
			}
		} else if (array_key_exists("title", $params) && array_key_exists("content", $params) && array_key_exists("groupid", $params) && array_key_exists("individ", $params)) {
			try {
				$data = $this->model->editSharedNote(
					array("userid" => $this->userData->data->id, "title" => $params["title"], "content" => $params["content"], "individ" => $params["individ"], "groupid" => $params["groupid"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to edit note.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	public function editTask() {
		$params = $this->getJSON();
		if (array_key_exists("title", $params) && array_key_exists("content", $params) && array_key_exists("individ", $params) && !array_key_exists("groupid", $params)) {
			try {
				$data = $this->model->editTask(array("userid" => $this->userData->data->id, "title" => $params["title"], "content" => $params["content"], "individ" => $params["individ"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to edit tasks.",
					"exception" => $e->getMessage()
				));
			}
		} else if (array_key_exists("title", $params) && array_key_exists("content", $params) && array_key_exists("groupid", $params) && array_key_exists("individ", $params)) {
			try {
				$data = $this->model->editSharedTask(
					array("userid" => $this->userData->data->id, "title" => $params["title"], "content" => $params["content"], "individ" => $params["individ"], "groupid" => $params["groupid"]));
				$this->sendJSON($data);
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to edit tasks.",
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
	public function deleteNote() {
		$params = $this->getJSON();
		if (array_key_exists("individ", $params) && !array_key_exists("groupid", $params)) {
			try {
				$this->model->deleteNote(array("userid" => $this->userData->data->id, "individ" => $params["individ"]));
				$this->error(200, array(
					"message" => "Note deleted succefully."
				));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to delete note.",
					"exception" => $e->getMessage()
				));
			}
		} else if (array_key_exists("individ", $params) && array_key_exists("groupid", $params)) {
			try {
				$this->model->deleteSharedNote(array("userid" => $this->userData->data->id, "individ" => $params["individ"], "groupid" => $params["groupid"]));
				$this->error(200, array(
					"message" => "Note deleted succefully."
				));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to delete note.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	public function deleteTask() {
		$params = $this->getJSON();
		if (array_key_exists("individ", $params) && !array_key_exists("groupid", $params)) {
			try {
				$this->model->deleteTask(array("userid" => $this->userData->data->id, "individ" => $params["individ"]));
				$this->error(200, array(
					"message" => "Note deleted succefully."
				));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to delete note.",
					"exception" => $e->getMessage()
				));
			}
		} else if (array_key_exists("individ", $params) && array_key_exists("groupid", $params)) {
			try {
				$this->model->deleteSharedTask(array("userid" => $this->userData->data->id, "individ" => $params["individ"], "groupid" => $params["groupid"]));
				$this->error(200, array(
					"message" => "Note deleted succefully."
				));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Unable to delete note.",
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