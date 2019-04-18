<?php

namespace models;

use core\Model;

class Notes extends Model {
	
	private function sortHtoL($a, $b) {
		if ($a == $b) {
        	return 0;
    	}
    	return ($a > $b) ? -1 : 1;
	}
	
	//GET
	public function loadAll($obj) {
		$notes = $this->loadNotes($obj);
		$tasks = $this->loadTasks($obj);
		$sharedNotes = $this->loadSharedNotes($obj);
		$sharedTasks = $this->loadSharedTasks($obj);
		$data = array_merge($notes, $tasks, $sharedNotes, $sharedTasks);
		usort($data,  array($this, "sortHtoL"));
		return $data;
	}
	
	public function loadNotes($obj) {
		$data = $this->db->row('SELECT noteId, individ, type, title, content, date FROM notes WHERE owner_id = :userid AND type = :type', array("userid" => $obj["userid"], "type" => 0));
		return $data;
	}
	
	public function loadTasks($obj) {
		$data = $this->db->row('SELECT noteId, individ, type, title, content, date FROM notes WHERE owner_id = :userid AND type = :type', array("userid" => $obj["userid"], "type" => 1));
		for ($i = 0; $i < count($data); $i++) {
			$tasks = $this->db->row('SELECT todoId, task, done FROM tasks WHERE noteAssignId ='.$data[$i]['noteId']);
			$data[$i]['content'] = $tasks;
		}
		return $data;
	}
	
	public function loadSharedNotes($obj) {
		$data = array();
		$groupIDs = $this->db->row('SELECT groupID FROM groupmems WHERE userID = :userID', array('userID' => $obj["userid"]));
		if (isset($groupIDs)) {
			for ($i = 0; $i < count($groupIDs); $i++) {
				$groupName = $this->db->column('SELECT groupName FROM groups WHERE groupID = :groupID', array('groupID' => $groupIDs[$i]['groupID']));
				$sharedContent = $this->db->row('SELECT contentID, tbName, individ FROM sharedcontent WHERE groupID = :groupID', array('groupID' => $groupIDs[$i]['groupID']));
				for ($u = 0; $u < count($sharedContent); $u++) {
					if ($sharedContent[$u]['tbName'] == 'notes') {
						$noteDetails = $this->db->row('SELECT individ, type, owner_id FROM notes WHERE noteId = :contentID', array("contentID" => $sharedContent[$u]['contentID']));
						if ($noteDetails[0]["type"] == 2 || $noteDetails[0]["type"] == 0) {
							$note = $this->loadNote(array("userid" => $noteDetails[0]["owner_id"], "type" => $noteDetails[0]["type"]), $noteDetails[0]["individ"]);
							$note['individ'] = $sharedContent[$u]['individ'];
							$note['groupId'] = $groupIDs[$i]['groupID'];
							$note['groupName'] = $groupName;
							array_push($data, $note);
						}
					}
				}
			}
			return $data;
		}
		return false;
	}
	
	public function loadSharedTasks($obj) {
		$data = array();
		$groupIDs = $this->db->row('SELECT groupID FROM groupmems WHERE userID = :userID', array('userID' => $obj["userid"]));
		if (isset($groupIDs)) {
			for ($i = 0; $i < count($groupIDs); $i++) {
				$groupName = $this->db->column('SELECT groupName FROM groups WHERE groupID = :groupID', array('groupID' => $groupIDs[$i]['groupID']));
				$sharedContent = $this->db->row('SELECT contentID, tbName, individ FROM sharedcontent WHERE groupID = :groupID', array('groupID' => $groupIDs[$i]['groupID']));
				for ($u = 0; $u < count($sharedContent); $u++) {
					if ($sharedContent[$u]['tbName'] == 'notes') {
						$noteDetails = $this->db->row('SELECT individ, type, owner_id FROM notes WHERE noteId = :contentID', array("contentID" => $sharedContent[$u]['contentID']));
						if ($noteDetails[0]["type"] == 3 || $noteDetails[0]["type"] == 1) {
							$note = $this->loadTask(array("userid" => $noteDetails[0]["owner_id"], "type" => $noteDetails[0]["type"]), $noteDetails[0]["individ"]);
							$note['individ'] = $sharedContent[$u]['individ'];
							$note['groupId'] = $groupIDs[$i]['groupID'];
							$note['groupName'] = $groupName;
							array_push($data, $note);
						}
					}
				}
			}
			return $data;
		} 
		return false;
	}
	
	public function loadSharedNote($obj) {
		$data = array();
		if ($this->db->column('SELECT groupID FROM groupmems WHERE userID = :userID AND groupID = :groupID', array('userID' => $obj["userid"], 'groupID' => $obj['groupid']))) { 
			$shrdContent = $this->db->row('SELECT tbName, individ, contentID FROM sharedcontent WHERE groupID = :groupID AND individ = :individ', array('individ' => $obj['id'], 'groupID' => $obj['groupid']));
			$groupName = $this->db->column('SELECT groupName FROM groups WHERE groupID = :groupID', array('groupID' => $obj['groupid']));
			if (isset($shrdContent)) {
				if ($shrdContent[0]['tbName'] === 'notes') {
					$noteDetails = $this->db->row('SELECT individ, type, owner_id FROM notes WHERE noteId = :contentID', array("contentID" => $shrdContent[0]['contentID']));
					$note = $this->loadNote(array("userid" => $noteDetails[0]["owner_id"], "type" => $noteDetails[0]["type"]), $noteDetails[0]["individ"]);
					$note['individ'] = $shrdContent[0]['individ'];
					$note['groupId'] = $obj['groupid'];
					$note['groupName'] = $groupName;
					array_push($data, $note);
					return array_pop($data);
				}
			}
		}
		return false;
	}
	
	public function loadSharedTask($obj) {
		$data = array();
		if ($this->db->column('SELECT groupID FROM groupmems WHERE userID = :userID AND groupID = :groupID', array('userID' => $obj["userid"], 'groupID' => $obj['groupid']))) { 
			$shrdContent = $this->db->row('SELECT tbName, individ, contentID FROM sharedcontent WHERE groupID = :groupID AND individ = :individ', array('individ' => $obj['id'], 'groupID' => $obj['groupid']));
			$groupName = $this->db->column('SELECT groupName FROM groups WHERE groupID = :groupID', array('groupID' => $obj['groupid']));
			if (isset($shrdContent)) {
				if ($shrdContent[0]['tbName'] === 'notes') {
					$noteDetails = $this->db->row('SELECT individ, type, owner_id FROM notes WHERE noteId = :contentID', array("contentID" => $shrdContent[0]['contentID']));
					$note = $this->loadTask(array("userid" => $noteDetails[0]["owner_id"], "type" => $noteDetails[0]["type"]), $noteDetails[0]["individ"]);
					$note['individ'] = $shrdContent[0]['individ'];
					$note['groupId'] = $obj['groupid'];
					$note['groupName'] = $groupName;
					array_push($data, $note);
					return array_pop($data);
				}
			}
		}
		return false;
	}
	
	public function loadNote($obj, $individ) {
		$data = $this->db->row('SELECT noteId, individ, type, title, content, date FROM notes WHERE owner_id = :userid AND type = :type AND individ = :individ', 
							   array("userid" => $obj["userid"], "type" => $obj["type"], "individ" => $individ));
		if (!empty($data)) return array_pop($data);
		else return false;
	}
	
	public function loadTask($obj, $individ) {
		$data = $this->db->row('SELECT noteId, individ, type, title, content, date FROM notes WHERE owner_id = :userid AND type = :type AND individ = :individ',
							   array("userid" => $obj["userid"], "type" => $obj["type"], "individ" => $individ));
		$tasks = $this->db->row('SELECT todoId, task, done FROM tasks WHERE noteAssignId ='.$data[0]['noteId']);
		$data[0]['content'] = $tasks;
		$data = array_pop($data);
		return $data;
	}
	
	//POST
	public function saveNote($obj) {
		$maxID = $this->db->column('SELECT MAX(individ) FROM notes WHERE owner_id = ' . $obj["userid"]);
		$params = [
			'individ' => $maxID + 1,
			'type' => 0,
			'title' => $obj['title'],
			'content' => $obj['content'],
			'owner_id' => $obj["userid"],
			'date' => date('Y-m-d H:i:s'),
		];
		if (strlen($params['title']) == 0 && strlen($params['content']) == 0 ) {
			throw new \InvalidArgumentException('Note is empty.');
		} else { 
			if ($obj['shareTo'] == 0) {
				$this->db->row('INSERT INTO notes (individ, type, title, content, owner_id, date) VALUES (:individ, :type, :title, :content, :owner_id, :date)', $params);
				$data = $this->loadNote(array("userid" => $obj["userid"], "type" => 0), $params["individ"]);
				return $data;
			} else if ($obj['shareTo'] > 0) {
				$members = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array('groupID' => $obj['shareTo']));
				if (in_array(array("userID" => $obj["userid"]), $members)) {
					$params['type'] = 2;
					$this->db->row('INSERT INTO notes (individ, type, title, content, owner_id, date) VALUES (:individ, :type, :title, :content, :owner_id, :date)', $params);
					$assignID = $this->db->lastInsertId();
					$maxIndivid = $this->db->column('SELECT MAX(individ) FROM sharedcontent WHERE groupID = :groupID', array('groupID' => $obj['shareTo'])) + 1;
					$this->db->row('INSERT INTO sharedcontent (contentID, tbName, individ, groupID) VALUES (:contentID, :tbName, :individ, :groupID)',
						array('contentID' => $assignID, 'tbName' => 'notes', 'individ' => $maxIndivid, 'groupID' => $obj['shareTo']));
					$data = $this->loadSharedNote(array("userid" => $obj["userid"], "id" => $maxIndivid, "groupid" => $obj['shareTo']));
					return $data;
				} else {
					throw new \InvalidArgumentException('Invalid group id provided.');
				}
			}
		}
	}
	
	//function for task addition to db
	private function writeTasks($array, $assignID) {
		$tasks = $array;
		$tasksNumber = count($tasks);
		if ($tasksNumber > 0) {
			for ($i = 0; $i < $tasksNumber; $i++) {
				$values = [
					'noteAssignId' => $assignID,
					'task' => $tasks[$i]['task'],
					'done' => 0,
				];
				if (strlen($values['task']) != 0) {
					$this->db->row('INSERT INTO tasks (noteAssignId, task, done) VALUES (:noteAssignId, :task, :done)', $values);
				}
			}
			return true;
		}
		return false;
	}
	
	public function saveTask($obj) {
		$maxID = $this->db->column('SELECT MAX(individ) FROM notes WHERE owner_id = ' . $obj['userid']);
		$params = [
			'individ' => $maxID + 1,
			'type' => 1,
			'title' => $obj['title'],
			'content' => '',
			'owner_id' => $obj['userid'],
			'date' => date('Y-m-d H:i:s'),
		];
		if ($obj['shareTo'] == 0) {
			$this->db->row('INSERT INTO notes (individ, type, title, content, owner_id, date) VALUES (:individ, :type, :title, :content, :owner_id, :date)', $params);
			$assignID = $this->db->lastInsertId();
			if ($this->writeTasks($obj["content"], $assignID)) {
				$individ = $this->db->column('SELECT individ FROM notes WHERE noteId = ' . $assignID);
				$data = $this->loadTask(array("userid" => $obj["userid"], "type" => 1), $individ);
				return $data;
			} else throw new \InvalidArgumentException('Unable to save tasks.');
		} else if ($obj['shareTo'] > 0) {
			$params['type'] = 3;
			$members = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array('groupID' => $obj['shareTo']));
			if (in_array(array("userID" => $obj['userid']), $members)) {
				$this->db->row('INSERT INTO notes (individ, type, title, content, owner_id, date) VALUES (:individ, :type, :title, :content, :owner_id, :date)', $params);
				$assignID = $this->db->lastInsertId();
				$maxIndivid = $this->db->column('SELECT MAX(individ) FROM sharedcontent WHERE groupID = :groupID', array('groupID' => $obj['shareTo'])) + 1;
				$this->db->row('INSERT INTO sharedcontent (contentID, tbName, individ, groupID) VALUES (:contentID, :tbName, :individ, :groupID)',
						array('contentID' => $assignID, 'tbName' => 'notes', 'individ' => $maxIndivid, 'groupID' => $obj['shareTo']));
				if ($this->writeTasks($obj["content"], $assignID)) {
					$data = $this->loadSharedTask(array("userid" => $obj["userid"], "id" => $maxIndivid, "groupid" => $obj["shareTo"]));
					return $data;
				} else throw new \InvalidArgumentException('Unable to save tasks.');
			} else throw new \InvalidArgumentException('Invalid group id provided.');
		}
	}
	
	public function shareContent($obj) {
		$groupIDs = $this->db->row('SELECT groupID FROM groupmems WHERE userID = :userID', array('userID' => $obj['userid']));
		$groupNames = array();
		for ($i = 0; $i < count($groupIDs); $i++) {
			array_push($groupNames, $this->db->column('SELECT groupName FROM groups WHERE groupID = :groupID', array('groupID' => $groupIDs[$i]['groupID'])));
		}
		if (in_array($obj['groupName'], $groupNames)) {
			$posInArray = array_search($obj['groupName'], $groupNames);
			$noteID = $this->db->column('SELECT noteId FROM notes WHERE individ = :individ AND owner_id = :userid', array('individ' => $obj['recID'], 'userid' => $obj['userid']));
			if (!empty($noteID)) {
				if (!$this->db->column('SELECT contentID FROM sharedcontent WHERE contentID =' . $noteID)) {
					$maxIndivid = $this->db->column('SELECT MAX(individ) FROM sharedcontent WHERE groupID = ' . $groupIDs[$posInArray]['groupID']) + 1;
					$this->db->row('INSERT INTO sharedcontent (contentID, tbName, individ, groupID) VALUES (:contentID, :tbName, :individ, :groupID)',
						array('contentID' => $noteID, 'tbName' => 'notes', 'individ' => $maxIndivid, 'groupID' => $groupIDs[$posInArray]['groupID']));
					$content = array("individ" => (string)$maxIndivid, "groupId" => $groupIDs[$posInArray]['groupID']);
					return $content;
				} else throw new \InvalidArgumentException('This record is already shared.');
			} else throw new \InvalidArgumentException('Content with such ID does not exist.');
		} else throw new \InvalidArgumentException('Group with such name does not exist.');
	}
	
	//PUT
	public function editNote($obj) {
		if (strlen($obj["content"]) > 0 || strlen($obj["title"]) > 0) {
			$note = $this->db->row('SELECT noteId FROM notes WHERE individ = :individ AND owner_id = :userid', array("individ" => $obj["individ"], 'userid' => $obj["userid"]));
			if (!empty($note)) {
				$params['noteId'] = $note[0]['noteId'];
				$this->db->query('UPDATE notes SET title = :title, content = :content WHERE noteId = :noteId',
					array('title' => $obj['title'], 'content' => $obj['content'], 'noteId' => $params['noteId']));
				$data = $this->loadNote(array("userid" => $obj["userid"], "type" => 0), $obj["individ"]);
				return $data;
			} else {
				throw new \InvalidArgumentException('Invalid note id provided.');
			}
		} else throw new \InvalidArgumentException('Note is empty.');
	}
	
	public function editSharedNote($obj) {
		if (strlen($obj["content"]) > 0 || strlen($obj["title"]) > 0) {
			if ($this->db->query('SELECT userID FROM groupmems WHERE groupID = :groupid AND userID = :userID', array("groupid" => $obj["groupid"], "userID" => $obj["userid"]))) {
				$contentID = $this->db->column('SELECT contentID FROM sharedcontent WHERE groupID = :groupid AND individ = :individ',
											   array("groupid" => $obj["groupid"], "individ" => $obj["individ"]));
				if (!empty($contentID)) {
					$note = $this->db->row('SELECT noteId FROM notes WHERE noteId = :noteid', array("noteid" => $contentID));
					$obj['noteId'] = $note[0]['noteId'];
					$this->db->query('UPDATE notes SET title = :title, content = :content WHERE noteId = :noteId',
						array('title' => $obj['title'], 'content' => $obj['content'], 'noteId' => $obj['noteId']));
					$data = $this->loadSharedNote(array("userid" => $obj["userid"], "id" => $obj["individ"], "groupid" => $obj['groupid']));
					return $data;
				} else {
					throw new \InvalidArgumentException('Invalid parameters provided.');
				}
			} else {
				throw new \InvalidArgumentException('Invalid group id provided.');
			}
		} else {
			throw new \InvalidArgumentException('Note is empty.');
		}
	}
	
	public function editTask($obj) {
		$params = [
			'title' => $obj['title'],
			'content' => $obj['content'],
		];
		if (count($params['content']) > 0 && strlen($params['title']) > 0) {
			$task = $this->db->row('SELECT noteId, individ, title FROM notes WHERE individ = :individ AND owner_id = :userid', array("individ" => $obj["individ"], "userid" => $obj["userid"]));
			if (!empty($task)) {
				if ($params['title'] !== $task[0]['title']) {
					$this->db->query('UPDATE notes SET title = :title WHERE noteId = :noteId',
							array('title' => $params['title'], 'noteId' => $task[0]['noteId']));
				}
				$origcontent = $this->db->row('SELECT * FROM tasks WHERE noteAssignId = :noteid', array('noteid' => $task[0]['noteId']));
				if (count($params['content']) > count($origcontent)) {
					for ($i = count($origcontent); $i < count($params['content']); $i++) {
						if (strlen($params['content'][$i]['task']) > 0) {
							$this->db->query('INSERT INTO tasks (noteAssignId, task, done) VALUES (:noteAssignId, :task, :done)', array('noteAssignId' => $task[0]['noteId'], 'task' => $params['content'][$i]['task'], 'done' => $params['content'][$i]['done']));
						}
					}
				} else if (count($params['content']) < count($origcontent)) {
					for ($i = count($params['content']); $i < count($origcontent); $i++) {
						$maxID = $this->db->column('SELECT MAX(todoId) FROM tasks WHERE noteAssignId = :noteAssignId', array("noteAssignId" => $task[0]['noteId']));
						$this->db->query('DELETE FROM tasks WHERE noteAssignId = :noteAssignId AND todoId = :todoId',
									   array("noteAssignId" => $task[0]['noteId'], "todoId" => $maxID));
						unset($origcontent[$i]);
					}
				}
				for ($i = 0; $i < count($origcontent); $i++) {
					if ($params['content'][$i] !== $origcontent[$i]['task']) {
						$this->db->query('UPDATE tasks SET task = :task, done = :done WHERE todoId = :todoId AND noteAssignId = :noteAssignId', array('task' => $params['content'][$i]['task'], 'done' => $params['content'][$i]['done'], 'todoId' => $origcontent[$i]['todoId'], 'noteAssignId' => $task[0]['noteId']));
					}
				}
				$data = $this->loadTask(array("userid" => $obj['userid'], "type" => 1), $task[0]["individ"]);
				return $data;
			} else {
				throw new \InvalidArgumentException('Invalid note ID provided.');
			}
		} else {
			throw new \InvalidArgumentException('Note is empty.');
		}
	}
	
	public function editSharedTask($obj) {
		$params = [
			'title' => $obj['title'],
			'content' => $obj['content'],
		];
		if (count($params['content']) > 0 && strlen($params['title']) > 0) {
			if ($this->db->query('SELECT userID FROM groupmems WHERE groupID = :groupid AND userID = :userID', array('groupid' => $obj['groupid'], 'userID' => $obj['userid']))) {
				$contentID = $this->db->column('SELECT contentID FROM sharedcontent WHERE groupID = :groupid AND individ = :individ',
											   array("groupid" => $obj["groupid"], "individ" => $obj["individ"]));
				if (!empty($contentID)) {
					$task = $this->db->row('SELECT noteId, title FROM notes WHERE noteId = :noteid', array('noteid' => $contentID));//gaut individ is sharedContent
					$shrdIndivid = $this->db->column('SELECT individ FROM sharedcontent WHERE contentID = :noteid', array('noteid' => $contentID));
					if ($params['title'] !== $task[0]['title']) {
						$this->db->row('UPDATE notes SET title = :title WHERE noteId = :noteId',
									   array('title' => $params['title'], 'noteId' => $task[0]['noteId']));
					}
					$origcontent = $this->db->row('SELECT * FROM tasks WHERE noteAssignId = :noteid', array('noteid' => $task[0]['noteId']));
					if (count($params['content']) > count($origcontent)) {
						for ($i = count($origcontent); $i < count($params['content']); $i++) {
							if (strlen($params['content'][$i]['task']) > 0) {
								$this->db->row('INSERT INTO tasks (noteAssignId, task, done) VALUES (:noteAssignId, :task, :done)', array('noteAssignId' => $task[0]['noteId'], 'task' => $params['content'][$i]['task'], 'done' => $params['content'][$i]['done']));
							}
						}
					} else if (count($params['content']) < count($origcontent)) {
						for ($i = count($params['content']); $i < count($origcontent); $i++) {
							$maxID = $this->db->column('SELECT MAX(todoId) FROM tasks WHERE noteAssignId = :noteAssignId', array("noteAssignId" => $task[0]['noteId']));
							$this->db->row('DELETE FROM tasks WHERE noteAssignId = :noteAssignId AND todoId = :todoId',
										   array("noteAssignId" => $task[0]['noteId'], "todoId" => $maxID));
							unset($origcontent[$i]);
						}
					}
					for ($i = 0; $i < count($origcontent); $i++) {
						if ($params['content'][$i] !== $origcontent[$i]['task']) {
							$this->db->row('UPDATE tasks SET task = :task, done = :done WHERE todoId = :todoId AND noteAssignId = :noteAssignId', array('task' => $params['content'][$i]['task'], 'done' => $params['content'][$i]['done'], 'todoId' => $origcontent[$i]['todoId'], 'noteAssignId' => $task[0]['noteId']));
						}
					}
					$data = $this->loadSharedTask(array("userid" => $obj["userid"], "id" => $obj["individ"], "groupid" => $obj['groupid']));
					return $data;
				} else {
					throw new \InvalidArgumentException('Invalid note ID provided.');
				}
			}
		} else {
			throw new \InvalidArgumentException('Note is empty.');
		}
	}
	
	//DELETE
	public function deleteNote($obj) {
		$note = $this->db->column('SELECT noteId FROM notes WHERE individ = :individ AND owner_id = :userid', array('individ' => $obj['individ'], 'userid' => $obj['userid']));
		if (!empty($note)) {
			$shared = $this->db->column('SELECT groupID FROM sharedcontent WHERE contentID = :contentID', array("contentID" => $note));
			if (empty($shared))
				$this->db->row('DELETE FROM notes WHERE noteId = :id', array('id' => $note));
			else throw new \InvalidArgumentException('Invalid note ID provided. Note is shared!');
		} else {
			throw new \InvalidArgumentException('Invalid note ID provided.');
		}
	}

	public function deleteSharedNote($obj) {
		if ($this->db->query('SELECT userID FROM groupmems WHERE groupID = :groupid AND userID = :userID', array("groupid" => $obj["groupid"], "userID" => $obj["userid"]))) {
			$contentID = $this->db->column('SELECT contentID FROM sharedcontent WHERE groupID = :groupid AND individ = :individ',
										   array("groupid" => $obj["groupid"], "individ" => $obj["individ"]));
			if (!empty($contentID)) {
				$note = $this->db->row('SELECT noteId, type FROM notes WHERE noteId = :noteid', array("noteid" => $contentID));
				$obj['noteId'] = $note[0]["noteId"];
				if ($note[0]["type"] == 2) {
					$this->db->row('DELETE FROM notes WHERE noteId = :noteId',
								   array('noteId' => $obj['noteId']));
				}
				$this->db->row('DELETE FROM sharedcontent WHERE contentID = :contentID',
							   array("contentID" => $contentID));
			} else {
				throw new \InvalidArgumentException('Invalid parameters provided.');
			}
		} else {
			throw new \InvalidArgumentException('Invalid group id provided.');
		}
	}
	
	public function deleteTask($obj) {
		$note = $this->db->column('SELECT noteId FROM notes WHERE individ = :individ AND owner_id = :userid', array("individ" => $obj["individ"], "userid" => $obj["userid"]));
		if (!empty($note)) {
			$shared = $this->db->column('SELECT groupID FROM sharedcontent WHERE contentID = :contentID', array("contentID" => $note));
			if (empty($shared)) {
				if ($this->db->query('DELETE FROM tasks WHERE noteAssignId = :noteId', array('noteId' => $note))) {
					$this->db->row('DELETE FROM notes WHERE noteId = :id', array('id' => $note));
				}
			} else throw new \InvalidArgumentException('Invalid note ID provided. Note is shared!');
		} else throw new \InvalidArgumentException('Invalid note ID provided.');
	}
	
	public function deleteSharedTask($obj) {
		if ($this->db->query('SELECT userID FROM groupmems WHERE groupID = :groupid AND userID = :userID', array("groupid" => $obj["groupid"], "userID" => $obj["userid"]))) {
			$contentID = $this->db->column('SELECT contentID FROM sharedcontent WHERE groupID = :groupid AND individ = :individ',
										   array("groupid" => $obj["groupid"], "individ" => $obj["individ"]));
			if (!empty($contentID)) {
				$note = $this->db->row('SELECT noteId, type FROM notes WHERE noteId = :noteid', array("noteid" => $contentID));
				$obj['noteId'] = $note[0]["noteId"];
				if ($note[0]["type"] == 3) {
					if ($this->db->row('DELETE FROM notes WHERE noteId = :noteId', array('noteId' => $obj['noteId']))) {
						$this->db->query('DELETE FROM tasks WHERE noteAssignId = :noteId', array('noteId' => $note[0]['noteId']));
					}
				}
				$this->db->row('DELETE FROM sharedcontent WHERE contentID = :contentID',
							   array("contentID" => $contentID));
			} else {
				throw new \InvalidArgumentException('Invalid parameters provided.');
			}
		} else {
			throw new \InvalidArgumentException('Invalid group id provided.');
		}
	}
}

?>