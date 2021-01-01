<!--
	Copyright 2012  Varun Verma  (email : varunverma@varunverma.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
-->
<?php

require 'GCMFunctions.php';
require_once("Class.GooglePushNotification.php");
require_once("Class.WindowsPushNotification.php");

createDBConnection();

// Start Logging
global $file_name;
$file_name = __DIR__ . "/log/" . date("Y-n-j") . ".log";

$log = "========== Custom Notifications Job Started ========== " . PHP_EOL;
__write_log($log);

global $mid, $subject, $content;
global $GCM_IDs, $WPN_IDs, $APN_IDs;

$GCM_IDs = array();
$WPN_IDs = array();
$APN_IDs = array();

message_to_upgrade_hj();
//message_to_upgrade_dj();
//message_to_upgrade_ss();
//download_hs();
//download_spellathon();
//send_delete_posts_command();
//send_update_posts_command();

$log = "========== Custom Notifications Job Finished ========== " . PHP_EOL;
__write_log($log);

function send_delete_posts_command(){

	global $GCM_IDs;

	$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE AppID = 1 AND Platform = 'Android'";
	
	__execute_device_query($device_query);
	
	$message = "DeletePost";
	$postIds = '2609';

	$gcm_notifications = new GooglePushNotification($GCM_IDs);
	$gcm_notifications->sendMessageWithPostIds($message,$postIds);
	$gcm_notifications->printResult();

}

function send_update_posts_command(){

	global $GCM_IDs;

	$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE AppID = 1 AND Platform = 'Android'";
	
	__execute_device_query($device_query);
	
	$message = "SyncPostIds";
	$postIds = '2609';

	$gcm_notifications = new GooglePushNotification($GCM_IDs);
	$gcm_notifications->sendMessageWithPostIds($message,$postIds);
	$gcm_notifications->printResult();

}

function download_spellathon(){
	
	$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE AppID <> 10 AND Platform = 'Android'";
	
	__execute_device_query($device_query);
	
	global $mid, $subject, $content;
	$mid = 123;
	$subject = "Try our new game : Spellathon";
	$content = "Dear Users," . 
				"<br><br>We are excited to launch our new game app : <b>Spellathon</b>." . 
				"<br><br>Spellathon helps you learn new English words and improve your vocabulary." .
				'<br><br>Please download the app from: <a href="https://play.google.com/store/apps/details?id=com.ayansh.spellathon.android">Google Play Store</a>' . 
				"<br><br>Once again, Thankyou for using our app !";
	
	__send_notifications();

}

function download_hs(){
	
	$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE AppID IN (1,7) AND Platform = 'Android'";
	
	__execute_device_query($device_query);
	
	global $mid, $subject, $content;
	$mid = 123;
	$subject = "Try our new App - Hindi Shayari";
	$content = "Dear Users," . 
				"<br><br>We are excited to introduce our new app : <b>Hindi Shayari</b> especially for our Love birds and Shayari Lovers." . 
				'<br><br>Please download the app from: <a href="https://play.google.com/store/apps/details?id=com.ayansh.hindishayari.android">Google Play Store</a>' . 
				"<br><br>Once again, Thankyou for using our app !";
	
	__send_notifications();
}

function message_to_upgrade_ss(){
	
	$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE AppID = 11 AND Platform = 'Android'";
	
	__execute_device_query($device_query);
	
	global $mid, $subject, $content;
	$mid = 123;
	$subject = "A new version of app is available";
	$content = "Thankyou for using our app !" . 
						"<br><br>A new update is available. Kindly update your app as soon as possible." . 
						'<br><br>To update, go to <a href="https://play.google.com/store/apps/details?id=com.ayansh.swagstatus.android">Google Play Store</a>' . 
						"<br><br>Thank you once again.";
	
	__send_notifications();
}

function message_to_upgrade_hj(){
	
	//$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices_cp WHERE AppID = 1 AND Platform = 'Android'";
	$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices_cp WHERE Platform = 'Android'";
	
	__execute_device_query($device_query);
	
	global $mid, $subject, $content;
	$mid = 123;
	$subject = "Hindi Jokes App is back... A new version of app is available";
	$content = "Thankyou for using our app !" . 
						"<br><br>The app is back with latest collection of new jokes" . 
						"<br><br>A new update is also available. Kindly update your app as soon as possible." . 
						"<br><br>The app comes with a new cool card layout UI." . 
						"<br><br>Share you jokes directly with your WhatsApp contacts in a single click." . 
						'<br><br>To update, go to <a href="https://play.google.com/store/apps/details?id=com.ayansh.hindijokes.android">Google Play Store</a>' . 
						"<br><br>Thank you once again.";
	
	__send_notifications();
}

function message_to_upgrade_dj(){
	
	$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE AppID = 7 AND AppVersion <> '53' AND Platform = 'Android'";
	
	__execute_device_query($device_query);
	
	global $mid, $subject, $content;
	$mid = 123;
	$subject = "A new version of app is available";
	$content = "Thankyou for using our app !" . 
						"<br><br>A new update is available. Kindly update your app as soon as possible." . 
						"<br><br>In the new version we have added support for Memes (Image based Jokes)." . 
						"<br><br>If you do not want Memes, you can switch it off from settings." . 
						'<br><br>To update, go to <a href="https://play.google.com/store/apps/details?id=com.ayansh.hindijokes.android">Google Play Store</a>' . 
						"<br><br>Thank you once again.";
	
	__send_notifications();
}

function __send_notifications(){
	
	// Send Custom Notifications
	global $mid, $subject, $content;
	global $GCM_IDs;
	global $WPN_IDs;
	global $APN_IDs;
		
	$gcm_notifications = new GooglePushNotification($GCM_IDs);
	$gcm_notifications->sendInfoMessage($subject,$content,$mid);
	$gcm_notifications->printResult();

	$wpn_notifications = new WindowsPushNotification($WPN_IDs);
	$wpn_notifications->sendInfoMessage($subject,$content,$mid);
	$wpn_notifications->printResult();

}

function __execute_device_query($device_query){
	
	global $db;
	global $GCM_IDs, $WPN_IDs, $APN_IDs;

	$GCM_IDs = array();
	$WPN_IDs = array();
	$APN_IDs = array();
	
	echo "Query : " . $device_query . "<br><br>";

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
	
	echo "GCM Count : " . count($GCM_IDs) . "<br>";
	echo "WPN Count : " . count($WPN_IDs) . "<br>";
	//var_dump($GCM_IDs);
	
}

function __write_log($log){
	
	global $file_name;
	//file_put_contents($file_name, $log, FILE_APPEND);
	
}

?>