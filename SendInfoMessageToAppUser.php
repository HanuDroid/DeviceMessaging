<?php

require 'GCMFunctions.php';
require_once("Class.GooglePushNotification.php");
require_once("Class.WindowsPushNotification.php");

createDBConnection();

$iid = $_POST['iid'];
$app_url = $_POST['app_url'];
$subject = $_POST['subject'];
$content = $_POST['content'];
$output_type = $_POST['output_type'];
$message_id = $_POST['message_id'];

$registrationIDs = array();

global $db;

$device_query = "SELECT * FROM hanu_devices as devices INNER JOIN hanu_applications as app ON app.ID = devices.AppId WHERE devices.InstanceId='$iid' AND app.BlogURL = '$app_url'";

$stmt = $db->prepare($device_query);
$sqlVars = array();

if (!$stmt->execute($sqlVars)){
	$stmt = null;
	die("Error while selecting reg Ids");
}
else{
	
	$GCM_IDs = array();
	$WPN_IDs = array();
	$APN_IDs = array();
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			
		$app_id = $row['AppID'];
		
		if($row['Platform'] == "Android"){
			$GCM_IDs["$app_id"][] = $row['RegId'];
		}
		else if($row['Platform'] == "Windows" || $row['Platform'] == "WindowsPhone"){
			$WPN_IDs["$app_id"][] = $row['RegId'];
		}
		else{
			$APN_IDs[] = $row['RegId'];
		}
		
	}

	$stmt = null;
}

// Send Info Messages to GCMFunctions
if(!empty($GCM_IDs)){
	
	$gcm_notifications = new GooglePushNotification($GCM_IDs);
	$gcm_notifications->sendInfoMessage($subject,$content,$message_id);
	
	if($output_type == "Return"){	
		$gcm_notifications->processResult();
	}
	else{
		$gcm_notifications->printResult();
	}
	
}

if(!empty($WPN_IDs)){
	
	$wpn_notifications = new WindowsPushNotification($WPN_IDs);
	$wpn_notifications->sendInfoMessage($subject,$content,$message_id);
	
	if($output_type == "Return"){	
		$wpn_notifications->processResult();
	}
	else{
		$wpn_notifications->printResult();
	}
}

?>