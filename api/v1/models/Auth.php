<?php

namespace models;

use core\Model;
use libs\mailer\Mailer;

class Auth extends Model {
	
	//GET
	public function getUser($params) {
		if (!empty($params["email"])) {
			$result = $this->db->row('SELECT id, name, surname, password, salt, email, activated FROM users WHERE email = :email', $params);
			return $result;
		}
	}
	
	//POST
	
	private function random($length) {
		//generate salt for passwords
		$bytes = random_bytes($length);
		return bin2hex($bytes);
	}
	
	public function registerUser($obj) {
		if (empty($this->getUser(array("email" => $obj["email"])))) {
			if (strlen($obj["password"]) >= 8) {
				$salt = $this->random(32);
				$hashedpswd = hash("sha256", $obj['password'] . $salt, false);
				$data = [
					"name" => $obj["name"],
					"surname" => $obj["surname"],
					"password" => $hashedpswd,
					"email" => $obj["email"],
					"salt" => $salt
				];
				$activationKey = hash('sha256', $data['email'] . time());
				$data['activated'] = 0;
				$this->db->row('INSERT INTO users (name, surname, password, salt, email, activated) VALUES (:name, :surname, :password, :salt, :email, :activated)', $data);
				$userID = $this->db->lastInsertId();
				$params = [
					'userID' => $userID,
					'code' => $activationKey,
					'date' => date('Y-m-d'),
					'type' => 0
				];
				$this->db->row('INSERT INTO emailActCodes (userID, code, date, type) VALUES (:userID, :code, :date, :type)', $params);
				$ml = new Mailer;
				$ml->sendActLink($obj['email'], $activationKey);
			} else throw new \InvalidArgumentException("Provided password is shorter than 8 characters!");
		} else throw new \InvalidArgumentException("Provided email is already in use!");
	}
	
	public function sendRestoreCode($obj) {
		//send password reset code
		$resend = true;
		$ml = new Mailer;
		$user = $this->db->row('SELECT id, activated FROM users WHERE email = :email', array('email' => $obj['email']));
		if (!empty($user)) {
			if ($user[0]["activated"] != 0) {
				$activationKey = $this->db->column('SELECT code FROM emailActCodes WHERE userID = :userid AND type = 1', array('userid' => $user[0]["id"]));
				if (empty($activationKey)) {
					$activationKey = hash('sha256', $obj['email'] . time());
					$resend = false;
				}
				$params = [
					'userID' => $user[0]["id"],
					'code' => $activationKey,
					'date' => date('Y-m-d'),
					'type' => 1
				];
				if ($resend == false) {
					if ($this->db->query('INSERT INTO emailActCodes (userID, code, date, type) VALUES (:userID, :code, :date, :type)', $params)) {
						$ml->sendResetPswdLink($obj['email'], $activationKey);
					}
				} else $ml->sendResetPswdLink($obj['email'], $activationKey);
			} else throw new \InvalidArgumentException("This account is not activated.");
		} else throw new \InvalidArgumentException("User with such email address is not found.");
	}
	
	public function resendActCode($obj) {
		//resend activation
		$ml = new Mailer;
		$user = $this->db->row('SELECT id, activated FROM users WHERE email = :email', array('email' => $obj['email']));
		if (!empty($user)) {
			if ($user[0]['activated'] == 0) { 
				$activationKey = $this->db->column('SELECT code FROM emailActCodes WHERE userID = :userid AND type = 0', array('userid' => $user[0]['id']));
				if (!empty($activationKey)) {
					$ml->sendActLink($obj['email'], $activationKey);
				} else {
					$activationKey = hash('sha256', $obj['email'] . time());
					$params = [
						'userID' => $user[0]['id'],
						'code' => $activationKey,
						'date' => date('Y-m-d'),
						'type' => 0
					];
					$this->db->row('INSERT INTO emailActCodes (userID, code, date, type) VALUES (:userID, :code, :date, :type)', $params);
					$ml->sendActLink($obj['email'], $activationKey);
				}
			} else throw new \InvalidArgumentException("Account is already activated.");
		} else throw new \InvalidArgumentException("User with such email is not found.");
	}
	
	//PUT
	public function changePassword($obj) {
		$user = $this->db->row('SELECT id, password, salt FROM users WHERE id = :userid', array("userid" => $obj["userid"]));
		$user = array_pop($user);
		$oldSalt = $user['salt'];
		$oldhashedpswd = hash("sha256", $obj['pswd'] . $oldSalt, false);
		if ($oldhashedpswd === $user['password']) {
			if (strlen($obj['npswd']) >= 8) {
				$newSalt = $this->random(32);
				$newHashedpswd = hash("sha256", $obj['npswd'] . $newSalt, false);
				$this->db->row('UPDATE users SET password = :password, salt = :salt WHERE id = :userid', array('password' => $newHashedpswd, 'salt' => $newSalt, 'userid' => $user['id']));
			} else throw new \InvalidArgumentException("Provided password is shorter than 8 characters!");
		} else throw new \InvalidArgumentException("Current password does not match.");
	}
	
	public function resetUserPassword($obj) {
		$userID = $this->db->column('SELECT userID FROM emailActCodes WHERE code = :code AND type = 1', array('code' => $obj['code']));
		if (!empty($userID)) {
			if (strlen($obj["password"]) >= 8) {
				$salt = $this->random(32);
				$hashedpswd = hash("sha256", $obj['password'] . $salt, false);
				$this->db->query('UPDATE users SET password = :password, salt = :salt WHERE id = :userID', array('password' => $hashedpswd, 'salt' => $salt, 'userID' => $userID));
				$this->db->query('DELETE FROM emailActCodes WHERE code = :code AND userID = :userID AND type = 1', array('code' => $obj['code'], 'userID' => $userID));
			} else throw new \InvalidArgumentException("Provided password is shorter than 8 characters!");
		} else throw new \InvalidArgumentException("Invalid password reset code.");
	}
	
	public function activateAccount($obj) {
		$DBUserCode = $this->db->row('SELECT userID, code, date, type FROM emailActCodes WHERE code = :code', array('code' => $obj['hash']));
		if (array_key_exists('code', $DBUserCode[0]) && $DBUserCode[0]['code'] == $obj['hash']) {
			if ($this->db->query('UPDATE users SET activated = :status WHERE id = :id', array('status' => true, 'id' => $DBUserCode[0]['userID']))) {
				$this->db->row('DELETE FROM emailActCodes WHERE userID = :userid AND type = 0', array('userid' => $DBUserCode[0]['userID']));
			}
		} else throw new \InvalidArgumentException("Invalid account activation hash.");
	}
}

?>