<?php

namespace controllers;

use libs\jwt\JWT;
use core\Controller;

class AuthController extends Controller {
	
	public function run() {
		//nothing to do
	}
	
	//GET
	public function loadAll() {
		//nothing to do
	}
	
	//POST
	public function sendLogin() {
		$headers = getallheaders();
		$json['authorization'] = explode(':', base64_decode($headers['Authorization']));
		$json['email'] = $json['authorization'][0];
		$json['password'] = $json['authorization'][1];
		$user = $this->model->getUser(array('email' => $json['email']));
		if (!empty($user)) {
			$hashedpswd = hash("sha256", $json['password'] . $user[0]['salt'], false);
			if ($hashedpswd === $user[0]['password']) {
				if ($user[0]['activated'] == 1) {
					$user = array_pop($user);
					$jwt = $this->genJwt($user);
					$this->error(200, array(
						"message" => "Successful login.",
						"jwt" => $jwt
					));
				} else {
					$this->error(422, array(
						"message" => "Authentication failed.",
						"exception" => "Account is not activated"
					));
				}
			} else {
				$this->error(401, array(
					"message" => "Authentication failed.",
					"exception" => "Password is incorrect."
				));
			}
		} else {
			$this->error(404, array(
				"message" => "Authentication failed."
			));
		}
	}
	
	public function sendRegister() {
		$json = $this->getJSON();
		if (array_key_exists("email", $json) && array_key_exists("name", $json) && array_key_exists("surname", $json)  && array_key_exists("password", $json)) {
			try {
				$this->model->registerUser($json);
				$this->sendJSON(array("message" => "User is succesfully registered."));
			} catch (\Exception $e) {
				$this->error(422, array(
					"message" => "Unable to register new user.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(401, array(
				"message" => "Registration failed. Invalid parameters."
			));
		}
	}
	
	public function sendRestore() {
		//send restore code
		$json = $this->getJSON();
		if (array_key_exists("email", $json)) {
			try {
				$this->model->sendRestoreCode($json);
				$this->sendJSON(array("message" => "Restore code is sent to your email."));
			} catch (\Exception $e) {
				$this->error(422, array(
					"message" => "Unable to send a restore code.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	public function sendActivation() {
		//resend activation code
		$json = $this->getJSON();
		if (array_key_exists("email", $json)) {
			try {
				$this->model->resendActCode($json);
				$this->sendJSON(array("message" => "Restore code is sent to your email."));
			} catch (\Exception $e) {
				$this->error(422, array(
					"message" => "Unable to send a activation code.",
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
	public function editPassword() {
		$userData = $this->validateJWT();
		if ($userData == false) exit();
		$params = $this->getJSON();
		if ($params !== false && array_key_exists("password", $params) && array_key_exists("newPassword", $params)) {
			try {
				$data = $this->model->changePassword(array("userid" => $userData->data->id, "pswd" => $params["password"], "npswd" => $params["newPassword"]));
				$this->sendJSON(array("message" => "Password was changed successfully."));
			} catch (\Exception $e) {
				$this->error(400, array(
					"message" => "Password was not changed",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	public function editRestore() {
		//change password
		$json = $this->getJSON();
		if (array_key_exists("code", $json) && array_key_exists("password", $json)) {
			try {
				$this->model->resetUserPassword($json);
				$this->sendJSON(array("message" => "Password is succesfully changed."));
			} catch (\Exception $e) {
				$this->error(422, array(
					"message" => "Unable to reset user's password.",
					"exception" => $e->getMessage()
				));
			}
		} else {
			$this->error(400, array(
				"message" => "Required parameters are not provided."
			));
		}
	}
	
	public function editActivation() {
		$json = $this->getJSON();
		if (array_key_exists("hash", $json)) {
			try {
				$this->model->activateAccount($json);
				$this->sendJSON(array("message" => "Account is successfully activated."));
			} catch (\Exception $e) {
				$this->error(422, array(
					"message" => "Unable to activate account.",
					"exception" => $e->getMessage()
				));
			}
		}
	}
}

?>