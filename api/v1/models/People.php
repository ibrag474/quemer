<?php

namespace models;

use core\Model;
use libs\notificator\Notificator;

class People extends Model {
	
	// GET
	public function known($obj) {
		$results = $this->db->row('SELECT id, areFriends, userid1, userid2, date FROM peoplerelation WHERE userid1 = :userid OR userid2 = :userid', array("userid" => $obj["userid"]));
		$resultsLen = count($results);
		if ($resultsLen > 0) {
			$result = array();
			$return = array();
			for ($i = 0; $i < count($results); $i++) {
				if ($results[$i]['userid1'] != $obj["userid"]) {
					$tmp = [ 'userid' => $results[$i]['userid1'], ];
					$userInfo = $this->db->row('SELECT name FROM users WHERE id = :userid', $tmp);
					$result['myid'] = $obj["userid"];
					$result['id'] = $results[$i]['id'];
					$result['areFriends'] = $results[$i]['areFriends']; 
					$result['userid1'] = $results[$i]['userid1'];
					$result['userid2'] = $results[$i]['userid2'];					
					$result['name'] = $userInfo[0]['name']; 
					$result['date'] = $results[$i]['date'];
				} else {
					$tmp = [ 'userid' => $results[$i]['userid2'], ];
					$userInfo = $this->db->row('SELECT name FROM users WHERE id = :userid', $tmp);
					$result['myid'] = $obj["userid"];
					$result['id'] = $results[$i]['id'];
					$result['areFriends'] = $results[$i]['areFriends']; 
					$result['userid1'] = $results[$i]['userid1'];
					$result['userid2'] = $results[$i]['userid2']; 
					$result['name'] = $userInfo[0]['name']; 
					$result['date'] = $results[$i]['date'];
				}
				array_push($return, $result);
			}
			return $return;
		} 
		return false;
	}
	
	public function search($obj) {
		$results = $this->db->row('SELECT id, name, surname FROM users WHERE name = :name', array("name" => $obj["name"]));
		for ($i = 0; $i < count($results); $i++) {
			$id = $results[$i]["id"];
			$status = $this->db->row('SELECT areFriends FROM peoplerelation WHERE (userid1 = :id AND userid2 = :myid) OR (userid1 = :myid AND userid2 = :id)', array("id" => $id, "myid" => $obj["userid"]));
			if (!empty($status)) {
				if ($status[0]["areFriends"] == 1) {
					$results[$i]["status"] = '2';
				} else $results[$i]["status"] = '1';
			} else $results[$i]["status"] = '0';
		}
		return $results;
	}
	
	// POST
	public function invite($obj) {
		$results = $this->db->row('SELECT id, areFriends, userid1, userid2 FROM peoplerelation WHERE userid1 = :userid AND userid2 = :id',
								  array("userid" => $obj["userid"],"id" => $obj["id"]));
		if (empty($results)) {
			if ($obj['id'] !== $obj["userid"]) {
				if ($this->db->column('SELECT count(id) FROM users WHERE id = :id', array("id" => $obj["id"])) > 0) {
					$values = ['areFriends' => 0, 'userid1' => $obj["userid"], 'userid2' => $obj['id'], 'date' => date('Y-m-d H:i:s')];
					$this->db->query('INSERT INTO peoplerelation (areFriends, userid1, userid2, date) VALUES (:areFriends, :userid1, :userid2, :date)', $values);
					$notif = new Notificator;
					$query = $notif->createNotification("people.invite", $obj["userid"], $obj["id"]);
					$this->db->query($query[0], $query[1]);
				} else {
					throw new \InvalidArgumentException('User does not exist.');
				}
			} else {
				throw new \InvalidArgumentException('User can not be invited by himself.');
			}
		} else {
			throw new \InvalidArgumentException('User is already invited or known.');
		}	
	}
	
	//PUT
	public function acceptInvitation($obj) {
		$results = $this->db->row('SELECT areFriends, userid1, userid2 FROM peoplerelation WHERE id = :id', array("id" => $obj["id"]));
		if (!empty($results)) {
			if ($obj["userid"] == $results[0]['userid2'] || $obj["userid"] == $results[0]['userid1']) {
				if ($results[0]['areFriends'] == 0 && $results[0]['userid1'] !== $obj["userid"]) {
					$this->db->query("UPDATE peoplerelation SET areFriends = '1' WHERE id = :id", array("id" => $obj["id"]));
				} else {
					throw new \InvalidArgumentException('Unable to accept invitation.');
				}
			} else {
				throw new \InvalidArgumentException('Invalid relation id provided.');
			}
		} else {
			throw new \InvalidArgumentException('Relation with such id is not found.');
		}
	}
	
	//DELETE
	public function deleteKnown($obj) {
		$results = $this->db->row('SELECT areFriends, userid1, userid2 FROM peoplerelation WHERE id = :id',
								  array("id" => $obj["id"]));
		if (!empty($results)) {
			if ($obj["userid"] == $results[0]['userid2'] || $obj["userid"] == $results[0]['userid1']) {
				$this->db->query("DELETE FROM peoplerelation WHERE id = :id", array("id" => $obj["id"]));
			} else {
				throw new \InvalidArgumentException('Invalid relation id provided.');
			}
		} else {
			throw new \InvalidArgumentException('Relation with such id is not found.');
		}
	}
	
}