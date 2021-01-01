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
	This should be run as batch job every 10 min.
	This will select 1300 Ids (see configuration) every 10 min and send them a message to sync.
-->
<?php

require 'GCMFunctions.php';
require_once("Class.GooglePushNotification.php");
require_once("Class.WindowsPushNotification.php");

createDBConnection();

$time = $_GET['time'];
if($time == ""){
	
	$hour = date("G");
	$min = date("i");
	
}
else{
	list($hour, $min) = split('[:]', $time);
}

$log = date('Y/m/d H:i:s') . " : Current Time is: " . $hour . ":" . $min . PHP_EOL;
echo $log . "<br><br>";

$qrt_rate = 1300;

$hour = $hour % 12;
$min = floor($min / 10);

$offset = $qrt_rate * 6 * $hour + $qrt_rate * $min;

$lim = $qrt_rate;
//$lim = $_GET['limit'];

global $db;

//$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices where ID > $min LIMIT $lim";
//$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE ID IN(106803,106809,106799,107020)";
//$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices order by ID Limit $lim OFFSET $offset";
//$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE AppID = 1 AND Platform Like '%Windows%' Limit $lim";
$device_query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE InstanceID = 'dNg524xq78w'";

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

//*
$reg_id_bundles = array_chunk($GCM_IDs, 800);
		
foreach($reg_id_bundles as $GCM_ID_Bundle){
	
	$timestamp = "test";
	$gcm_notifications = new GooglePushNotification($GCM_ID_Bundle);
	$gcm_notifications->notifyToSyncUsingTimeStamp($timestamp);
	$gcm_notifications->printResult();
	
}

//*/
		
// Notify Windows Devices to Sync
/*

$test = json_encode($WPN_IDs);
echo "Count : " . count($WPN_IDs) . "<br><br>";

$wpn_notifications = new WindowsPushNotification($WPN_IDs);
$wpn_notifications->notifyToSyncUsingTimeStamp($timestamp);
$wpn_notifications->printResult();
//*/

?>