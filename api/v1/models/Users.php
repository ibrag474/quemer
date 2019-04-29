<?php

namespace models;

use core\Model;

class Users extends Model {
	
	//GET
	public function loadMe($obj) {
		$data = $this->db->row('SELECT name, surname, email, activated FROM users WHERE id = :userid', array("userid" => $obj["userid"]));
		if (!empty($data)) {
			return array_pop($data);
		} else throw new \InvalidArgumentException('User is not found.');
	}
	
	//POST
	
	//PUT
	public function editAccDetails($obj) {
		$user = $this->db->row('SELECT surname, activated FROM users WHERE id = :userid', array("userid" => $obj["userid"]));
		$user = array_pop($user);
		if (!empty($user)) {
			if ($user['activated'] == 1) {
				$this->db->row('UPDATE users SET surname = :surname WHERE id = :userid', array("userid" => $obj["userid"], "surname" => $obj["surname"]));
			} else throw new \InvalidArgumentException('This account is not activated.');
		} else throw new \InvalidArgumentException('User with that id is not found.');
	} 
	
	//DELETE	
}