<?php

namespace models;

use core\Model;

class Groups extends Model {
	
	//GET
	public function loadAll($obj) {
		$myGroups = $this->db->row('SELECT groupID, groupName, adminID FROM groups WHERE groupID IN (SELECT groupID FROM groupmems WHERE userID = :userID)', array("userID" => $obj["userid"]));
		if (!empty($myGroups)) {
			for ($i = 0; $i < count($myGroups); $i++) {
				$members = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array("groupID" => $myGroups[$i]["groupID"]));
				for ($u = 0; $u < count($members); $u++) {
					$user = $this->db->column('SELECT name FROM users WHERE id = :userID', array("userID" => $members[$u]["userID"]));
					$members[$u]['name'] = $user;
				}
				$myGroups[$i]['members'] = $members;
			}
			return $myGroups;
		} else throw new \InvalidArgumentException('Groups are not found');
	}
	
	public function loadGroup($obj) {
		$data = $this->db->row('SELECT groupID, groupName, adminID FROM groups WHERE groupID IN (SELECT groupID FROM groupmems WHERE userID = :userID AND groupID = :groupID)',
							   array("userID" => $obj["userid"], "groupID" => $obj["groupid"]));
		$data[0]["myid"] = $obj['userid'];
		if (!empty($data)) {
			$data[0]["members"] = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array("groupID" => $data[0]["groupID"]));
			for ($i = 0; $i < count($data[0]["members"]); $i++) {
				$data[0]["members"][$i]["name"] = $this->db->column('SELECT name FROM users WHERE id = :userID', array("userID" => $data[0]["members"][$i]["userID"]));
			}
			return array_pop($data);
		} else throw new \InvalidArgumentException('Group are not found.');
	}
	
	//POST
	private function checkGroupMembers($members, $me) {
		//checks if future members are friends
		$status = true;
		if (in_array($me, $members) && count($members) > 1) {
			unset($members[array_search($me, $members)]);
		} else {
			$status = false;
		}
		for ($i = 0; $i < count($members); $i++) {
			if (!empty($members[$i])) {
				$res = $this->db->column('SELECT areFriends FROM peoplerelation WHERE (userid1 = :me AND userid2 = :userid) OR (userid1 = :userid AND userid2 = :me)',
				array('userid' => $members[$i], 'me' => $me));
				if ($res == 0) $status = false;
			}
		}
		return $status;
	}
	
	public function createGroup($obj) {
		//add creator to list of added people
		if (!empty($obj["title"]) && !empty($obj["content"])) {
			array_push($obj["content"], $obj["userid"]);
			if ($this->checkGroupMembers($obj["content"], $obj["userid"])) {
				$usersGroup = $this->db->column('SELECT groupID FROM groups WHERE adminID = :adminID AND groupName = :title', array("adminID" => $obj["userid"], "title" => $obj["title"]));
				if (empty($usersGroup)) {
					$this->db->row('INSERT INTO groups (groupName, adminID) VALUES (:groupName, :adminID)', array("groupName" => $obj["title"], "adminID" => $obj["userid"]));
					$groupID = $this->db->lastInsertId();
					for ($i = 0; $i < count($obj['content']); $i++) {
						$this->db->row('INSERT INTO groupmems (userID, groupID) VALUES (:userID, :groupID)', array("userID" => $obj["content"][$i], "groupID" => $groupID));
					}
					$data = $this->loadGroup(array("groupid" => $groupID, "userid" => $obj["userid"]));
					return $data;
				} else throw new \InvalidArgumentException('Group with same title already exists.');
			} else throw new \InvalidArgumentException('Invalid member list provided.');
		} else {
			throw new \InvalidArgumentException('Member list is empty or title is not filled out.');
		}
	}
	
	//PUT
	public function editGroup($obj) {
		if (!empty($obj["title"]) && !empty($obj["content"]) && $this->checkGroupMembers($obj["content"], $obj["userid"])) {
			$usersGroup = $this->db->row('SELECT groupID, groupName FROM groups WHERE adminID = :adminID AND groupID = :groupID', array("adminID" => $obj["userid"], "groupID" => $obj["groupid"]));
			$usersGroup = array_pop($usersGroup);
			if (!empty($usersGroup)) {
				$groupMembers = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array("groupID" => $usersGroup["groupID"]));
				if ($usersGroup["groupName"] != $obj["title"])
					$this->db->query('UPDATE groups SET groupName = :title WHERE groupID = :groupID', array("title" => $obj["title"], "groupID" => $usersGroup["groupID"]));
				
				for($i = 0; $i < count($groupMembers); $i++) {
					if (!in_array($groupMembers[$i]["userID"], $obj["content"])) {
						$this->db->row('DELETE FROM groupmems WHERE userID = :userID AND groupID = :groupID', array("userID" => $groupMembers[$i]["userID"], "groupID" => $usersGroup["groupID"]));
					}
				}
				$poppedgm = array();
				foreach($groupMembers as $gm) {
					array_push($poppedgm, $gm["userID"]);
				}
				for($i = 0; $i < count($obj["content"]); $i++) {
					if (in_array($obj["content"][$i], $poppedgm) == false) {
						$this->db->row('INSERT INTO groupmems (userID, groupID) VALUES (:userID, :groupID)', array("userID" => $obj["content"][$i], "groupID" => $usersGroup["groupID"]));
					}
				}
				$data = $this->loadGroup(array("groupid" => $usersGroup["groupID"], "userid" => $obj["userid"]));
				return $data;
			} else {
				throw new \InvalidArgumentException('You do not have rights to edit this group.');
			}
		} else throw new \InvalidArgumentException('Invalid data provided.');
	}
	
	//DELETE
	public function deleteGroup($obj) {
		$adminID = $this->db->column('SELECT adminID FROM groups WHERE groupID = :groupID', array('groupID' => $obj['groupid']));
		if ($adminID == $obj['userid']) {
			$sharedContent = $this->db->row('SELECT contentID, tbName, individ FROM sharedcontent WHERE groupID = :groupID', array('groupID' => $obj['groupid']));
			if (empty($sharedContent)) {
				$this->db->row('DELETE FROM groupmems WHERE groupID = :groupID', array('groupID' => $obj['groupid']));
				$this->db->row('DELETE FROM groups WHERE groupID = :groupID', array('groupID' => $obj['groupid']));
			} else throw new \InvalidArgumentException('Group has shared content!');
		} else {
			$groupMembers = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array('groupID' => $obj['groupid']));
			$poppedgm = array();
			foreach($groupMembers as $gm) {
				array_push($poppedgm, $gm["userID"]);
			}
			if (in_array($obj['userid'], $poppedgm)) {
				$this->db->row('DELETE FROM groupmems WHERE groupID = :groupID AND userID = :userID', array('groupID' => $obj['groupid'], 'userID' => $obj['userid']));
			} else throw new \InvalidArgumentException('Invalid group id provided, you are not member of that group.');
		}
	}
}

?>