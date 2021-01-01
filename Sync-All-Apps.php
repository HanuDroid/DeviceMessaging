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

<!--
	This should be run as batch job every 5 min.
	This will select 750 Ids (see configuration) every 5 min and send them a message to sync.
-->
<?php

require 'GCMFunctions.php';
require_once("Class.GooglePushNotification.php");
require_once("Class.WindowsPushNotification.php");

createDBConnection();

// Start Logging
global $file_name;
$file_name = __DIR__ . "/log/" . date("Y-n-j") . ".log";

$log = date('Y/m/d H:i:s') . "========== Sync Job Started ========== " . PHP_EOL;
write_log($log);

$time = $_GET['time'];
if($time == ""){
	
	$hour = date("G");
	$min = date("i");
	
}
else{
	list($hour, $min) = split('[:]', $time);
}

$log = date('Y/m/d H:i:s') . " : Current Time is: " . $hour . ":" . $min . PHP_EOL;
write_log($log);

$interval_rate = 1350;

// Interval Size in minutes
$interval_size = 10;

// Interval Count in 60 min.
$interval_count = 60 / $interval_size;	

$hour = $hour % 12;
$min = floor($min / $interval_size);

$offset = $interval_rate * $interval_count * $hour + $interval_rate * $min;

$lim = $interval_rate;

global $db;

//$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices where ID > $min LIMIT $lim";
//$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices order by ID Limit $lim OFFSET $offset";
$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE AppID IN (1,7) order by ID Limit $lim OFFSET $offset";

$log = date('Y/m/d H:i:s') . " : Query is: " . $device_query . PHP_EOL;
write_log($log);

//echo "Query : " . $device_query;

$stmt = $db->prepare($device_query);
$sqlVars = array();

if (!$stmt->execute($sqlVars)){
	$stmt = null;
	$log = date('Y/m/d H:i:s') . " : Error in selection! ". PHP_EOL;
	write_log($log);
	die("Error while selecting reg Ids");
}
else{
	
	$GCM_IDs = array();
	$WPN_IDs = array();
	$APN_IDs = array();
	
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		
		$app_id = $row['AppID'];
		
		if($row['Platform'] == "Android"){
			$GCM_IDs[] = $row['RegId'];
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

// Send Push Notifications

// Notify Google Devices to Sync
//var_dump($GCM_IDs);

$log = date('Y/m/d H:i:s') . " : Sending Sync Message to Google IDs " . PHP_EOL;
write_log($log);

$reg_id_bundles = array_chunk($GCM_IDs, 800);
		
foreach($reg_id_bundles as $GCM_ID_Bundle){
	
	$gcm_notifications = new GooglePushNotification($GCM_ID_Bundle);
	$gcm_notifications->notifyToSync();
	$gcm_notifications->printResult();

}
		
$log = date('Y/m/d H:i:s') . " : Sync message sent to all Google IDs " . PHP_EOL;
write_log($log);

// For Windows
$log = date('Y/m/d H:i:s') . " : Sending Sync Message to Windows IDs " . PHP_EOL;
write_log($log);

//*
$wpn_notifications = new WindowsPushNotification($WPN_IDs);
$wpn_notifications->notifyToSync();
$wpn_notifications->printResult();
//*/

$log = date('Y/m/d H:i:s') . " : Sync message sent to all Windows IDs " . PHP_EOL;
write_log($log);

$log = date('Y/m/d H:i:s') . "========== Sync Job Finished ========== " . PHP_EOL;
write_log($log);

function write_log($log){
	
	global $file_name;
	file_put_contents($file_name, $log, FILE_APPEND);
	
}

?>