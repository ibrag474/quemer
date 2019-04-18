<?php

namespace app\models;

use core\Model;
use lib\Mailer;

class Account extends Model {
	
	public function getUser($params) {
		$result = $this->db->row('SELECT id, password, salt, email, activated FROM users WHERE email = :email', $params);
		return $result;
	}
	
	public function addUsers($data = [], $actKey) {
		if (isset($actKey)) {
			$data['activated'] = 0;
			$this->db->row('INSERT INTO users (name, password, salt, email, activated) VALUES (:name, :password, :salt, :email, :activated)', $data);
			$userID = $this->db->lastInsertId();
			$params = [
				'userID' => $userID,
				'code' => $actKey,
				'date' => date('Y-m-d'),
				'type' => 0
			];
			$this->db->row('INSERT INTO emailActCodes (userID, code, date, type) VALUES (:userID, :code, :date, :type)', $params);
		}
	}
	
	public function activateAcc($obj) {
		$DBUserCode = $this->db->row('SELECT userID, code, date, type FROM emailActCodes WHERE code = :code', array('code' => $obj['hash']));
		if (array_key_exists('code', $DBUserCode[0]) && $DBUserCode[0]['code'] == $obj['hash']) {
			if ($this->db->query('UPDATE users SET activated = :status WHERE id = :id', array('status' => true, 'id' => $DBUserCode[0]['userID']))) {
				$this->db->row('DELETE FROM emailActCodes WHERE userID = :userid AND type = 0', array('userid' => $DBUserCode[0]['userID']));
				header("location:/account/login");
			}
		}
	}
	
	public function resendActCode($obj) {
		$ml = new Mailer;
		$user = $this->db->row('SELECT id, activated FROM users WHERE email = :email', array('email' => $obj['email']));
		if (isset($user)) {
			if ($user[0]['activated'] == 0) {
				$activationKey = $this->db->column('SELECT code FROM emailActCodes WHERE userID = :userid AND type = 0', array('userid' => $user[0]['id']));
				if (strlen($activationKey) > 1) {
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
			}
		}
	}
	
	public function sendPswdCode($obj) {
		$resend = true;
		$ml = new Mailer;
		$user = $this->db->column('SELECT id FROM users WHERE email = :email', array('email' => $obj['email']));
		if (!empty($user)) {
			$activationKey = $this->db->column('SELECT code FROM emailActCodes WHERE userID = :userid AND type = 1', array('userid' => $user));
			if (empty($activationKey)) {
				$activationKey = hash('sha256', $obj['email'] . time());
				$resend = false;
			}
			$params = [
				'userID' => $user,
				'code' => $activationKey,
				'date' => date('Y-m-d'),
				'type' => 1
			];
			if ($resend == false) {
				if ($this->db->query('INSERT INTO emailActCodes (userID, code, date, type) VALUES (:userID, :code, :date, :type)', $params)) {
					$ml->sendResetPswdLink($obj['email'], $activationKey);
				}
			} else $ml->sendResetPswdLink($obj['email'], $activationKey);
		}
	}
	
	public function changePswd($obj) {
		$userID = $this->db->column('SELECT userID FROM emailActCodes WHERE code = :code AND type = 1', array('code' => $obj['code']));
		if (!empty($userID)) {
			$this->db->query('UPDATE users SET password = :password, salt = :salt WHERE id = :userID', array('password' => $obj['password'], 'salt' => $obj['salt'], 'userID' => $userID));
			$this->db->query('DELETE FROM emailActCodes WHERE code = :code AND userID = :userID AND type = 1', array('code' => $obj['code'], 'userID' => $userID));
		} else http_response_code(422);
	}
}

?>