<?php

namespace models;

use core\Model;

class Users extends Model {
	
	//GET
	public function loadMe($obj) {
		$data = $this->db->row('SELECT name, email, activated FROM users WHERE id = :userid', array("userid" => $obj["userid"]));
		if (!empty($data)) {
			return array_pop($data);
		} else throw new \InvalidArgumentException('User is not found.');
	}
	
	//POST
	
	//PUT
	
	//DELETE	
}