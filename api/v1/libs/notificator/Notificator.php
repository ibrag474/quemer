<?php

namespace libs\Notificator;
	
class Notificator {
	
	private $notifTypes = array(
		"people.invite" => "invited you to become friends",
		"people.forget" => "deleted you from friends list",
		
	);
	
	public function createNotification($type, $senderID, $recipientID) {
		$date = date('Y-m-d H:i:s');
		return ['INSERT INTO notifications (senderID, recipientID, type, date, unread) VALUES (:senderID, :recipientID, :type, :date, :unread)',
									 array("senderID" => $senderID, "recipientID" => $recipientID, "type" => $type, "date" => $date, "unread" => false)];
	}
	
}

?>