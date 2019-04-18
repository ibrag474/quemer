<?php

namespace app\models;

use core\Model;

class App extends Model {
	//---show---
	public function getNotes() {
		$result = $this->db->row('SELECT type, title, content, date FROM notes WHERE owner_id = ' . $_SESSION['user_id']);
		return $result;
	}
		//add notes
	public function addNote($obj) {
		$maxID = $this->db->column('SELECT MAX(individ) FROM notes WHERE owner_id = ' . $_SESSION['user_id']);
		$params = [
			'individ' => $maxID + 1,
			'type' => 0,
			'title' => $obj['title'],
			'content' => $obj['content'],
			'owner_id' => $_SESSION['user_id'],
			'date' => date('Y-m-d H:i:s'),
		];
		if (strlen($params['title']) == 0 && strlen($params['content']) == 0 ) {
			http_response_code(422);
		} else { 
			if (array_key_exists('shareTo', $obj)) {
				if ($obj['shareTo'] == 0) {
					$this->db->row('INSERT INTO notes (individ, type, title, content, owner_id, date) VALUES (:individ, :type, :title, :content, :owner_id, :date)', $params);
					$this->loadNote(array('noteid' => $maxID + 1), 0);
				} else if ($obj['shareTo'] > 0) {
					$members = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array('groupID' => $obj['shareTo']));
					if (in_array(array("userID" => $_SESSION['user_id']), $members)) {
						$params['type'] = 2;
						$this->db->row('INSERT INTO notes (individ, type, title, content, owner_id, date) VALUES (:individ, :type, :title, :content, :owner_id, :date)', $params);
						$assignID = $this->db->lastInsertId();
						$maxIndivid = $this->db->column('SELECT MAX(individ) FROM sharedcontent WHERE groupID = :groupID', array('groupID' => $obj['shareTo'])) + 1;
						$this->db->row('INSERT INTO sharedcontent (contentID, tbName, individ, groupID) VALUES (:contentID, :tbName, :individ, :groupID)',
							array('contentID' => $assignID, 'tbName' => 'notes', 'individ' => $maxIndivid, 'groupID' => $obj['shareTo']));
						$this->loadSharedContent(array('noteid' => $maxIndivid, 'groupid' => $obj['shareTo']));
					}
				}
			}
		}
	}
	
	public function addTask($obj) {
		if (strlen($obj['title']) > 0) {
			$maxID = $this->db->column('SELECT MAX(individ) FROM notes WHERE owner_id = ' . $_SESSION['user_id']);
			$params = [
				'individ' => $maxID + 1,
				'type' => 1,
				'title' => $obj['title'],
				'content' => '',
				'owner_id' => $_SESSION['user_id'],
				'date' => date('Y-m-d H:i:s'),
			];
			if ($obj['shareTo'] == 0) {
				$this->db->row('INSERT INTO notes (individ, type, title, content, owner_id, date) VALUES (:individ, :type, :title, :content, :owner_id, :date)', $params);
				$assignID = $this->db->lastInsertId();
			} else if ($obj['shareTo'] > 0) {
				$params['type'] = 3;
				$members = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array('groupID' => $obj['shareTo']));
					if (in_array(array("userID" => $_SESSION['user_id']), $members)) {
						$this->db->row('INSERT INTO notes (individ, type, title, content, owner_id, date) VALUES (:individ, :type, :title, :content, :owner_id, :date)', $params);
						$assignID = $this->db->lastInsertId();
						$maxIndivid = $this->db->column('SELECT MAX(individ) FROM sharedcontent WHERE groupID = :groupID', array('groupID' => $obj['shareTo'])) + 1;
						$this->db->row('INSERT INTO sharedcontent (contentID, tbName, individ, groupID) VALUES (:contentID, :tbName, :individ, :groupID)',
							array('contentID' => $assignID, 'tbName' => 'notes', 'individ' => $maxIndivid, 'groupID' => $obj['shareTo']));
					}
			}
			$tasks = $obj['content'];
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
				if ($obj['shareTo'] == 0)
					$this->loadNote(array('noteid' => $maxID + 1), 0);
				else if ($obj['shareTo'] > 0) $this->loadSharedContent(array('noteid' => $maxIndivid, 'groupid' => $obj['shareTo']));
			} else http_response_code(422);
		} else http_response_code(422);
	}
	
	//load notes
	
	public function loadAll() {
		$toReturn = array();
		$params = [
			'userid' => $_SESSION['user_id'],
		];// ORDER BY noteId DESC
		$rowNotes = $this->db->row('SELECT noteId, individ, type, title, content, date FROM notes WHERE owner_id = :userid AND (type = 0 OR type = 1)', $params);
		for ($i = 0; $i < count($rowNotes); $i++) {
			$tmp = array();
			if ($rowNotes[$i]['type'] == 0) {
				$tmp['individ'] = $rowNotes[$i]['individ'];
				$tmp['type'] = $rowNotes[$i]['type'];
				$tmp['title'] = $rowNotes[$i]['title'];
				$tmp['content'] = $rowNotes[$i]['content'];
				$tmp['date'] = $rowNotes[$i]['date'];
			} else if ($rowNotes[$i]['type'] == 1) {
				$tmp['individ'] = $rowNotes[$i]['individ'];
				$tmp['type'] = $rowNotes[$i]['type'];
				$tmp['title'] = $rowNotes[$i]['title'];
				$rowTasks = $this->db->row('SELECT todoId, task, done FROM tasks WHERE noteAssignId = :noteId', array('noteId' => $rowNotes[$i]['noteId']));
				$tasks = array();
				for ($u = 0; $u < count($rowTasks); $u++) {
					$tasktmp = array();
					$tasktmp['todoId'] = $rowTasks[$u]['todoId'];
					$tasktmp['task'] = $rowTasks[$u]['task'];
					$tasktmp['done'] = $rowTasks[$u]['done'];
					array_push($tasks, $tasktmp);
				}
				$tmp['content'] = $tasks;
				$tmp['date'] = $rowNotes[$i]['date'];
			}
			array_push($toReturn, $tmp);
		}
		echo json_encode($toReturn);
	}
	
	public function loadNote($obj, $req) {
		if ($req == 0) {
			$params = [
				'noteid' => $obj['noteid'],
				'userid' => $_SESSION['user_id'],
			];
		} else if ($req == 1) {
			$params = [
				'noteid' => $obj['noteid'],
			];
		}
		if (strlen($params['noteid']) == 0) $params['noteid'] = $this->db->column('SELECT MAX(individ) FROM notes WHERE owner_id = ' . $_SESSION['user_id']);
		if ($req == 0)
			$note = $this->db->row('SELECT noteId, individ, type, title, content FROM notes WHERE owner_id = :userid AND individ = :noteid', $params);
		else if ($req == 1) $note = $this->db->row('SELECT noteId, individ, type, title, content FROM notes WHERE noteId = :noteid', $params);
		$note[0]['title'] = html_entity_decode($note[0]['title'], ENT_QUOTES); 
		if ($note[0]['type'] == 0) {
			$note[0]['content'] = html_entity_decode($note[0]['content'], ENT_QUOTES); 
		} else if ($note[0]['type'] == 1 || $note[0]['type'] == 3) {
			$tasks = $this->db->row('SELECT todoId, task, done FROM tasks WHERE noteAssignId ='.$note[0]['noteId']);
			$note[0]['content'] = $tasks;
		}
		if ($req == 0) 
			echo json_encode($note);
		else if ($req == 1) return $note;
	}
	
	//edit notes
	public function editNote($obj) {
		$params = [
			'title' => $obj['title'],
			'content' => $obj['content'],
		];
		$shared = false;
		if (strlen($params['content']) > 0 || strlen($params['title']) > 0) {
			if (!array_key_exists('noteid', $obj) && array_key_exists('individ', $obj)) {
				$shared = false;
				$note = $this->db->row('SELECT noteId FROM notes WHERE individ = :individ AND owner_id = :userid', array('individ' => $obj['individ'], 'userid' => $_SESSION['user_id']));
			}
			else if (array_key_exists('noteid', $obj) && !array_key_exists('individ', $obj)){
				if (array_key_exists('groupid', $obj))  {
					if ($this->db->query('SELECT userID FROM groupmems WHERE groupID = :groupid AND userID = :userID', array('groupid' => $obj['groupid'], 'userID' => $_SESSION['user_id']))) {
						if ($this->db->column('SELECT tbName FROM sharedcontent WHERE groupID = :groupid AND contentID = :noteid', array('groupid' => $obj['groupid'], 'noteid' => $obj['noteid']))) {
							$shared = true;
							$note = $this->db->row('SELECT noteId FROM notes WHERE noteId = :noteid', array('noteid' => $obj['noteid']));
							$shrdIndivid = $this->db->column('SELECT individ FROM sharedcontent WHERE contentID = :noteid', array('noteid' => $obj['noteid']));
						} else http_response_code(422);
					}
				} else http_response_code(422);
			}
			if ($note != null) {
				$params['noteId'] = $note[0]['noteId'];
				$this->db->query('UPDATE notes SET title = :title, content = :content WHERE noteId = :noteId',
					array('title' => $params['title'], 'content' => $params['content'], 'noteId' => $params['noteId']));
				if ($shared == true) {
					$this->loadSharedContent(array('noteid' => $shrdIndivid, 'groupid' => $obj['groupid']), 1);
				} else $this->loadNote(array('noteid' => $obj['individ']), 0);
			} else {
				http_response_code(422);
			}
		}
	}
	
	public function deleteRec($obj) {
		$fromGroup = false;
		if ($obj['noteid'] != null) {
			if (array_key_exists('noteid', $obj) && !array_key_exists('groupid', $obj)) 
				$note = $this->db->row('SELECT noteId, type FROM notes WHERE individ = :individ AND owner_id = :userid', array('individ' => $obj['noteid'], 'userid' => $_SESSION['user_id']));
			else if (array_key_exists('noteid', $obj) && array_key_exists('groupid', $obj)) {
				$fromGroup = true;
				$members = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array('groupID' => $obj['groupid']));
				if (in_array(array("userID" => $_SESSION['user_id']), $members)) {
					if ($this->db->query('SELECT contentID FROM sharedcontent WHERE individ = :noteID AND groupID = :groupID', array('noteID' => $obj['noteid'], 'groupID' => $obj['groupid'])))
						$contentID = $this->db->row('SELECT contentID FROM sharedcontent WHERE individ = :noteID AND groupID = :groupID', array('noteID' => $obj['noteid'], 'groupID' => $obj['groupid']));
						$note = $this->db->row('SELECT noteId, type FROM notes WHERE noteId = :noteId', array('noteId' => $contentID[0]['contentID']));
				}
			}	
			if ($note != null) {
				if ($note[0]['type'] == 0 || $note[0]['type'] == 2) {
					if ($this->db->query('SELECT contentID FROM sharedcontent WHERE contentID = :noteID', array('noteID' => $note[0]['noteId']))) {
						$this->db->row('DELETE FROM sharedcontent WHERE contentId = :contentID', array('contentID' => $note[0]['noteId']));
						//$this->db->row('DELETE FROM notes WHERE noteId = :id', array('id' => $note[0]['noteId']));
					} 
					if (!$fromGroup || $note[0]['type'] == 2)
						$this->db->row('DELETE FROM notes WHERE noteId = :id', array('id' => $note[0]['noteId']));
				} else if ($note[0]['type'] == 1 || $note[0]['type'] == 3) {
					if ($this->db->query('SELECT contentID FROM sharedcontent WHERE contentID = :noteID', array('noteID' => $note[0]['noteId']))) {
						$this->db->row('DELETE FROM sharedcontent WHERE contentId = :contentID', array('contentID' => $note[0]['noteId']));
						//$this->db->query('DELETE FROM tasks WHERE noteAssignId = :noteId', array('noteId' => $note[0]['noteId']));
						//$this->db->row('DELETE FROM notes WHERE noteId = :id', array('id' => $note[0]['noteId']));
					}
					if (!$fromGroup || $note[0]['type'] == 3) {
						if ($this->db->query('DELETE FROM tasks WHERE noteAssignId = :noteId', array('noteId' => $note[0]['noteId']))) {
							$this->db->row('DELETE FROM notes WHERE noteId = :id', array('id' => $note[0]['noteId']));
						}
					}
				}
			} else http_response_code(422);
		} else http_response_code(422);
	}
	
	public function editTask($obj) {
		$params = [
			'title' => $obj['title'],
			'content' => $obj['content'],
		];
		$shared = false;
		if (count($params['content']) > 0 && strlen($params['title']) > 0) {
			if (!array_key_exists('noteid', $obj) && array_key_exists('individ', $obj)) {
				$task = $this->db->row('SELECT noteId, title FROM notes WHERE individ = :individ AND owner_id = :userid', array('individ' => $obj['individ'], 'userid' => $_SESSION['user_id']));
				$shared = false;
			} else if (array_key_exists('noteid', $obj) && !array_key_exists('individ', $obj)) {
				if (array_key_exists('groupid', $obj))  {
					if ($this->db->query('SELECT userID FROM groupmems WHERE groupID = :groupid AND userID = :userID', array('groupid' => $obj['groupid'], 'userID' => $_SESSION['user_id']))) {
						if ($this->db->column('SELECT tbName FROM sharedcontent WHERE groupID = :groupid AND contentID = :noteid', array('groupid' => $obj['groupid'], 'noteid' => $obj['noteid']))) {
							$task = $this->db->row('SELECT noteId, title FROM notes WHERE noteId = :noteid', array('noteid' => $obj['noteid']));//gaut individ is sharedContent
							$shrdIndivid = $this->db->column('SELECT individ FROM sharedcontent WHERE contentID = :noteid', array('noteid' => $obj['noteid']));
							$shared = true;
						} else http_response_code(422);
					}
				} else http_response_code(422);
			}
			if ($task != null) {
				$params['noteId'] = $task[0]['noteId'];
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
				}
				for ($i = 0; $i < count($origcontent); $i++) {
					if ($params['content'][$i] !== $origcontent[$i]['task']) {
						$this->db->row('UPDATE tasks SET task = :task, done = :done WHERE todoId = :todoId', array('task' => $params['content'][$i]['task'], 'done' => $params['content'][$i]['done'], 'todoId' => $origcontent[$i]['todoId']));
					}
				}
				if ($shared == true)
					$this->loadSharedContent(array('noteid' => $shrdIndivid, 'groupid' => $obj['groupid']), 1);
				else $this->loadNote(array('noteid' => $obj['individ']), 0);
			} else {
				http_response_code(422);
			}
		}
	}
	
	public function deleteTask($obj) {
		$params = [
			'noteid' => $obj['noteid'],
			'taskPos' => $obj['taskPos'],
		];
		if (array_key_exists('noteid', $obj) && !array_key_exists('groupid', $obj)) {
			$noteId = $this->db->column('SELECT noteId FROM notes WHERE individ = :individ AND owner_id = :userid', array('individ' => $params['noteid'], 'userid' => $_SESSION['user_id']));
			$taskIds = $this->db->row('SELECT todoId FROM tasks WHERE noteAssignId = :noteId', array('noteId' => $noteId));
			$this->db->row('DELETE FROM tasks WHERE todoId = :id', array('id' => $taskIds[$params['taskPos']]['todoId']));
		} else if (array_key_exists('noteid', $obj) && array_key_exists('groupid', $obj)) {
			//if ($this->db->column('SELECT noteId FROM notes WHERE individ = :individ AND owner_id = :userid', array('individ' => $params['noteid'], 'userid' => $_SESSION['user_id'])))
		}
	}
	
	//---Groups---
	
	public function createGroup($obj) {
		$params = [
			'title' => $obj['title'],
			'content' => $obj['content'],
		];
		array_push($params['content'], $_SESSION['user_id']);
		if ($this->checkGroupMembers($params['content']) && strlen($params['title']) > 0) {
			$usersGroups = $this->db->row('SELECT groupName FROM groups WHERE adminID = :adminID', array('adminID' => $_SESSION['user_id']));
			if (!in_array($params['title'], $usersGroups)) {
				$this->db->row('INSERT INTO groups (groupName, adminID) VALUES (:groupName, :adminID)', array('groupName' => $params['title'], 'adminID' => $_SESSION['user_id']));
				$groupID = $this->db->lastInsertId();
				for ($i = 0; $i < count($params['content']); $i++) {
					$this->db->row('INSERT INTO groupmems (userID, groupID) VALUES (:userID, :groupID)', array('userID' => $params['content'][$i], 'groupID' => $groupID));
				}
				
			}
		}
	}
	
	private function checkGroupMembers($members) {
		for ($i = 0; $i < count($members); $i++) {
			if (strlen($members[$i]) != 0) {
				$res = $this->db->column('SELECT areFriends FROM peoplerelation WHERE userid1 OR userid2 = :userid AND userid1 OR userid2 = :me',
				array('userid' => $members[$i], 'me' => $_SESSION['user_id']));
				if ($res == 1) return true;
				else return false;
			}
		}
	}
	
	public function loadGroups($obj) {
		$myGroups = $this->db->row('SELECT groupID, groupName FROM groups WHERE groupID IN (SELECT groupID FROM groupmems WHERE userID = :userID)', array('userID' => $_SESSION['user_id']));
		for ($i = 0; $i < count($myGroups); $i++) {
			$members = $this->db->row('SELECT userID FROM groupmems WHERE groupID = :groupID', array('groupID' => $myGroups[$i]['groupID']));
			for ($u = 0; $u < count($members); $u++) {
				$user = $this->db->column('SELECT name FROM users WHERE id = :userID', array('userID' => $members[$u]['userID']));
				$members[$u]['name'] = $user;
			}
			$myGroups[$i]['members'] = $members;
		}
		echo json_encode($myGroups);
	}
	
	public function deleteGroup($obj) {
		$adminID = $this->db->column('SELECT adminID FROM groups WHERE groupID = :groupID', array('groupID' => $obj['groupID']));
		if ($adminID == $_SESSION['user_id']) {
			$sharedContent = $this->db->row('SELECT contentID, tbName, individ FROM sharedcontent WHERE groupID = :groupID', array('groupID' => $obj['groupID']));
			for ($i = 0; $i < count($sharedContent); $i++) {
				$content = $this->db->row('SELECT type FROM notes WHERE noteId = :noteId', array('noteId' => $sharedContent[$i]['contentID']));
				if ($content[0]['type'] == 2 || $content[0]['type'] == 3) $this->deleteRec(array('noteid' => $sharedContent[$i]['individ'], 'groupid' => $obj['groupID']));
			}
			$this->db->row('DELETE FROM groupmems WHERE groupID = :groupID', array('groupID' => $obj['groupID']));
			$this->db->row('DELETE FROM groups WHERE groupID = :groupID', array('groupID' => $obj['groupID']));
		}
	}
	
	public function loadSharedContent($obj) {
		$content = array();
		if (!array_key_exists('noteid', $obj) && !array_key_exists('groupid', $obj)) {
			$groupIDs = $this->db->row('SELECT groupID FROM groupmems WHERE userID = :userID', array('userID' => $_SESSION['user_id']));
			for ($i = 0; $i < count($groupIDs); $i++) {
				$groupName = $this->db->column('SELECT groupName FROM groups WHERE groupID = :groupID', array('groupID' => $groupIDs[$i]['groupID']));
				$sharedContent = $this->db->row('SELECT contentID, tbName, individ FROM sharedcontent WHERE groupID = :groupID', array('groupID' => $groupIDs[$i]['groupID']));
				for ($u = 0; $u < count($sharedContent); $u++) {
					if ($sharedContent[$u]['tbName'] == 'notes') {
						$params = array('noteid' => $sharedContent[$u]['contentID']);
						$note = $this->loadNote($params, 1);
						$note[0]['individ'] = $sharedContent[$u]['individ'];
						$note[0]['groupId'] = $groupIDs[$i]['groupID'];
						$note[0]['groupName'] = $groupName;
						array_push($content, $note);
					}
				}
			}
		//dedicated to load only one shared record
		} else if (array_key_exists('noteid', $obj) && array_key_exists('groupid', $obj)) {
			if ($this->db->column('SELECT groupID FROM groupmems WHERE userID = :userID AND groupID = :groupID', array('userID' => $_SESSION['user_id'], 'groupID' => $obj['groupid']))) {
				$shrdContent = $this->db->row('SELECT tbName, individ, contentID FROM sharedcontent WHERE groupID = :groupID AND individ = :individ', array('individ' => $obj['noteid'], 'groupID' => $obj['groupid']));
				$groupName = $this->db->column('SELECT groupName FROM groups WHERE groupID = :groupID', array('groupID' => $obj['groupid']));
				if (isset($shrdContent)) {
					if ($shrdContent[0]['tbName'] === 'notes') {
						$params = array('noteid' => $shrdContent[0]['contentID']);
						$note = $this->loadNote($params, 1);
						$note[0]['individ'] = $shrdContent[0]['individ'];
						$note[0]['groupId'] = $obj['groupid'];
						$note[0]['groupName'] = $groupName;
						array_push($content, $note[0]);
					}
				}
			}
		}
		echo json_encode($content);
	}
	
	public function shareContent($obj) {
		$groupIDs = $this->db->row('SELECT groupID FROM groupmems WHERE userID = :userID', array('userID' => $_SESSION['user_id']));
		$groupNames = array();
		$groups = array();
		for ($i = 0; $i < count($groupIDs); $i++) {
			array_push($groupNames, $this->db->column('SELECT groupName FROM groups WHERE groupID = :groupID', array('groupID' => $groupIDs[$i]['groupID'])));
		}
		if (in_array($obj['groupName'], $groupNames)) {
			$posInArray = array_search($obj['groupName'], $groupNames);
			$noteID = $this->db->column('SELECT noteId FROM notes WHERE individ = :individ AND owner_id = :userid', array('individ' => $obj['recID'], 'userid' => $_SESSION['user_id']));
			if (!empty($noteID)) {
				if (!$this->db->column('SELECT contentID FROM sharedcontent WHERE contentID =' . $noteID)) {
					$maxIndivid = $this->db->column('SELECT MAX(individ) FROM sharedcontent WHERE groupID = ' . $groupIDs[$posInArray]['groupID']) + 1;
					$this->db->row('INSERT INTO sharedcontent (contentID, tbName, individ, groupID) VALUES (:contentID, :tbName, :individ, :groupID)',
						array('contentID' => $noteID, 'tbName' => 'notes', 'individ' => $maxIndivid, 'groupID' => $groupIDs[$posInArray]['groupID']));
						$this->loadSharedContent(array('noteid' => $maxIndivid, 'groupid' => $groupIDs[$posInArray]['groupID']));
				}
			}
		}
	}
	
	//---people---
	public function loadPeople() {
		$params = [
			'userid' => $_SESSION["user_id"],
		];
		$results = $this->db->row('SELECT * FROM peoplerelation WHERE userid1 OR userid2 = :userid', $params);
		$resultsLen = count($results);
		if ($resultsLen > 0) {
			$result = array();
			$return = array();
			for ($i = 0; $i < count($results); $i++) {
				if ($results[$i]['userid1'] != $_SESSION["user_id"]) {
					$tmp = [ 'userid' => $results[$i]['userid1'], ];
					$userInfo = $this->db->row('SELECT name FROM users WHERE id = :userid', $tmp);
					$result['myid'] = $_SESSION["user_id"];
					$result['id'] = $results[$i]['id'];
					$result['areFriends'] = $results[$i]['areFriends']; 
					$result['userid1'] = $results[$i]['userid1'];
					$result['userid2'] = $results[$i]['userid2'];					
					$result['name'] = $userInfo[0]['name']; 
					$result['date'] = $results[$i]['date'];
				} else {
					$tmp = [ 'userid' => $results[$i]['userid2'], ];
					$userInfo = $this->db->row('SELECT name FROM users WHERE id = :userid', $tmp);
					$result['myid'] = $_SESSION["user_id"];
					$result['id'] = $results[$i]['id'];
					$result['areFriends'] = $results[$i]['areFriends']; 
					$result['userid1'] = $results[$i]['userid1'];
					$result['userid2'] = $results[$i]['userid2']; 
					$result['name'] = $userInfo[0]['name']; 
					$result['date'] = $results[$i]['date'];
				}
				array_push($return, $result);
			}
			echo json_encode($return);
		} else http_response_code(204);
	}
	
	public function searchPeople($obj) {
		$params = [
			'name' => $obj['name'],
		];
		$results = $this->db->row('SELECT id, name FROM users WHERE name = :name', $params);
			$json = json_encode($results);
			echo $json;
	}
	
	public function managePeople($obj) {
		$params = [
			'id' => $obj['id'],
		];
		$results = $this->db->row('SELECT areFriends, userid1, userid2 FROM peoplerelation WHERE id = :id', $params);
		if (count($results) > 0) {
			if ($_SESSION["user_id"] == $results[0]['userid2'] || $_SESSION["user_id"] == $results[0]['userid1']) {
				if ($results[0]['areFriends'] == 0 && $results[0]['userid1'] !== $_SESSION['user_id']) {
					$this->db->query("UPDATE peoplerelation SET areFriends = '1' WHERE id = :id", $params);
					$resp = ['resp' => 'accepted', 'areFriends' => 1];
					echo json_encode($resp);
				} else {
					$this->db->query("DELETE FROM peoplerelation WHERE id = :id", $params);
					$resp = ['resp' => 'deleted'];
					echo json_encode($resp);
				}
			}
		}
	}
	
	public function invitePeople($obj) {
		$params = [
			'id' => $obj['id'],
		];
		
		$results = $this->db->row('SELECT id, areFriends, userid1, userid2 FROM peoplerelation WHERE userid1 = '.$_SESSION["user_id"].' AND userid2 = :id', $params);
		if ($results == null) {
			if ($obj['id'] !== $_SESSION['user_id']) {
				if ($this->db->column('SELECT count(id) FROM users WHERE id = :id', $params) > 0) {
					$values = ['areFriends' => 0, 'userid1' => $_SESSION['user_id'], 'userid2' => $obj['id'], 'date' => date('Y-m-d H:i:s')];
					$this->db->query('INSERT INTO peoplerelation (areFriends, userid1, userid2, date) VALUES (:areFriends, :userid1, :userid2, :date)', $values);
					echo $obj['id'].' is invited.';
				} else {
					http_response_code(404);
				}
			} else {
				http_response_code(403);
			}
		} else {
			http_response_code(422);
		}
	}
}
?>