<?php

namespace app\controllers;

use core\Controller;
use lib\Mailer;

class AccountController extends Controller {
	
	private $obj;
	
	public function loginAction($params) {
		$this->view->layout = 'account';
		$this->view->render('Login | Quemer.com'/*, $vars*/);
	}
	
	public function registerAction($params) {
		$this->view->layout = 'account';
		$this->view->render('Register | Quemer.com');
	}
	
	public function restoreAction($params) {
		/*$this->obj = $this->getRequest();
		if (!empty($this->obj)) {
			if ($this->obj['act'] === 'actcode') {
				$this->model->resendActCode($this->obj);
				header("location:/account/login");
			} else if ($this->obj['act'] === 'pswdresetcode') {
				$this->model->sendPswdCode($this->obj);
			} else if ($this->obj['act'] === 'pswdreset') {
				$salt = $this->random(32);
				$this->obj['salt'] = $salt;
				$this->obj['password'] = hash("sha256", $this->obj['password'] . $salt, false);
				$this->model->changePswd($this->obj);
			}
		} else { */
		$this->view->layout = 'account';
		$this->view->render('Restore | Quemer.com');
		//}
	}
	
	public function activateAction($params) {
		/*if (strlen($params) > 1) {
			$obj = $this->getRequest($params);
			if ($obj != null) {
				$this->model->activateAcc($obj);
			}
		}*/
	}
	
	/*public function loginUser($obj) {
		$data = [
			'email' => $obj['email'],
		];
		$result = $this->model->getUser($data);
		if (!empty($result)) {
			$hashedpswd = hash("sha256", $obj['password'] . $result[0]['salt'], false);
			if ($result[0]['password'] === $hashedpswd) {
				if ($result[0]['activated'] == 1) {
					$_SESSION["user_id"] = $result[0]['id'];
					$_SESSION["logged_in"] = 1;
					$this->view->response("redirect", "/app/show");
				} else {
					$param = '{"act":"actcode", "email":"'.$data['email'].'"}';
					$this->view->response("message", "Your account is not activated. Please check your email and click the activation link.<a href='/account/restore/?param=". $param ."'>Resend</a>");
				}
			} else $this->view->response("message", "Incorrect email or password");
		} else $this->view->response("message", "Incorrect email or password");
	}
	//resenda padaryt, patikrint logina
	
	public function registerUser($obj) {
		$salt = $this->random(32);
		$hashedpswd = hash("sha256", $obj['password'] . $salt, false);
		$email = ['email' => $obj['email']];
		if ($this->model->getUser($email) == null) { 
			$data = [
				'name' => $obj['name'],
				'password' => $hashedpswd,
				'email' => $obj['email'],
				'salt' => $salt
			];
			$activationKey = hash('sha256', $data['email'] . time());
			$this->model->addUsers($data, $activationKey);
			$this->sendActLink($obj['email'], $activationKey);
			$this->view->response("message" ,"You have completed the registration process. Now you can <a href='/account/login'>login</a> but first activate your account with the link we sent to your email.");
		} else $this->view->response("message" ,"This email address had already been registered.");
	}
	
	private function sendActLink($email, $activationKey) {
		$ml = new Mailer;
		$ml->sendActLink($email, $activationKey);
	}
	
	public function random($length) {
		$bytes = random_bytes($length);
		return bin2hex($bytes);
	}
*/	
}

?>
