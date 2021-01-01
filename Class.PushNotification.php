<?php
/*

The abstract class Push Notification.

*/

require_once("GCMFunctions.php");

abstract class PushNotification {

	protected $regIDs;
	protected $result;
	protected $db;
	protected $app_id;
	protected $output = array();
	protected $summary_output = array();

	public function __construct($regIDs){
		
		$this->regIDs = $regIDs;
		$this->db = createDBConnection();
	}
	
	public function notifyToSync(){
		// Must be redefined in sub classes
	}
	
	public function sendPing(){
		// Must be redefined in sub classes
	}
	
	public function printResult(){
		
		foreach($this->output as $output_message){
			echo $output_message;
		}
		
	}
	
	public function printSummary(){
		
		foreach($this->summary_output as $output_message){
			echo $output_message;
		}
	}
	
	public function sendInfoMessage($subject,$content,$message_id=0){
		// Must be redefined in sub classes
	}
	
	public function sendUserData($user,$pwd){
		// Must be redefined in sub classes
	}
	
	public function sendMessage($message,$notif_message){
		// Must be redefined in sub classes
	}
	
	public function sendGenericMessage($message){
		// Must be redefined in sub classes
	}
	
	public function sendGenericMessageWithData($message,$message_data){
		// Must be redefined in sub classes
	}
	
	public function sendMessageWithPostIds($message,$postIds){
		
	}
	
	protected function deleteRegId($regId){
			
		$query = "DELETE FROM hanu_devices WHERE RegId = '$regId'";
		$stmt = $this->db->prepare($query);
		$sqlVars = array();
		$stmt->execute($sqlVars);
		$stmt = null;
	}

	protected function replaceRegId($oldRegId, $newRegId){
		
		$query = "UPDATE hanu_devices SET RegId = '$newRegId' WHERE RegId = '$regId'";
		$stmt = $this->db->prepare($query);
		$sqlVars = array();
		$stmt->execute($sqlVars);
		$stmt = null;
		
	}
}

?>