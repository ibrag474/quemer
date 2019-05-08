<?php

namespace models;

use core\Model;
use libs\notificator\Notificator;

class Notifications extends Model {
	
	public function loadAll() {
		$this->db->row('SELECT');
	}
}
	
?>