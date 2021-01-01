<?php

// These are my global variables.
global $db;

function getConfig(){

	$str = file_get_contents('config.json');
	$configData = json_decode($str);
	return $configData;
}

function createDBConnection(){
	
	$configData = getConfig();

	global $db;
	$host = $configData->db->host;
	$user = $configData->db->user;
	$pass = $configData->db->password;
	$database = $configData->db->gcm_dbname;
	
	$db = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $pass);
	return $db;
}

function get_gcm_summary(){
	
	global $db;
	$summary = array();
	
	$query = "select apps.Name as AppName, apps.BlogURL as BlogURL, 
			act.ActiveDeviceCount, inact.InActiveDeviceCount 
			from hanu_applications as apps 
			left outer join (select AppID, count(*) as ActiveDeviceCount from hanu_devices where IsActive = 'X' group by AppID) as act on act.AppID = apps.ID
			left outer join (select AppID, count(*) as InActiveDeviceCount from hanu_devices where IsActive = '' group by AppID) as inact  on inact.AppID = apps.ID
			order by act.ActiveDeviceCount desc";
			
	$stmt = $db->prepare($query);
	$sqlVars = array();

	if (!$stmt->execute($sqlVars)){
	}
	else{
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$summary[] = $row;
		}	
	}
	
	$stmt = null;
	return $summary;
}

function get_application_details($package){
	
	global $db;
	
	$query = "SELECT * FROM hanu_apps WHERE Package='$package' and Validity > current_timestamp LIMIT 1";
	$stmt = $db->prepare($query);
	$sqlVars = array();

	if (!$stmt->execute($sqlVars)){
		$stmt = null;
		return null;
	}
	else{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt = null;
		return $row;
	}
}

function get_application_details_by_blog_url($blog_url){
	
	global $db;
	
	$query = "SELECT * FROM hanu_applications WHERE BlogURL='$blog_url' and Validity > current_timestamp LIMIT 1";
	$stmt = $db->prepare($query);
	$sqlVars = array();

	if (!$stmt->execute($sqlVars)){
		$stmt = null;
		return null;
	}
	else{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt = null;
		return $row;
	}
}

function get_all_registered_devices(){
	
	global $db;
	
	$registrationIDs = array();
	$query = "SELECT RegId, AppID, Platform FROM hanu_devices";
	$stmt = $db->prepare($query);
	$sqlVars = array();

	if (!$stmt->execute($sqlVars)){
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
				$APN_IDs["$app_id"][] = $row['RegId'];
			}
			
		}
		$stmt = null;
		
		$registrationIDs = array(
							'Google' 	=> $GCM_IDs,
							'Windows'	=> $WPN_IDs,
							'Apple'		=> $APN_IDs
							);
	}
	
	$stmt = null;
	return $registrationIDs;
	
}

function get_registered_devices_for_app($app_id){
	
	global $db;
	
	$query = "SELECT RegId, Platform FROM hanu_devices WHERE AppID = $app_id";
	$stmt = $db->prepare($query);
	$sqlVars = array();

	if (!$stmt->execute($sqlVars)){
	}
	else{
		
		$GCM_IDs = array();
		$WPN_IDs = array("$app_id" => array());
		$APN_IDs = array();
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			
			if($row['Platform'] == "Android"){
				$GCM_IDs["$app_id"][] = $row['RegId'];
			}
			else if($row['Platform'] == "Windows" || $row['Platform'] == "WindowsPhone"){
				$WPN_IDs["$app_id"][] = $row['RegId'];
			}
			else{
				$APN_IDs["$app_id"][] = $row['RegId'];
			}
			
		}
		$stmt = null;
		
		$registrationIDs = array(
							'Google' 	=> $GCM_IDs,
							'Windows'	=> $WPN_IDs,
							'Apple'		=> $APN_IDs
							);
		
	}
	
	$stmt = null;
	return $registrationIDs;
}

function get_reg_ids_for_iid($iid){
	
	global $db;
	
	$query = "SELECT RegId, AppID, Platform FROM hanu_devices WHERE InstanceID = '$iid'";
	$stmt = $db->prepare($query);
	$sqlVars = array();

	if (!$stmt->execute($sqlVars)){
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
				$APN_IDs["$app_id"][] = $row['RegId'];
			}
			
		}
		$stmt = null;
		
		$registrationIDs = array(
							'Google' 	=> $GCM_IDs,
							'Windows'	=> $WPN_IDs,
							'Apple'		=> $APN_IDs
							);
	}
	
	$stmt = null;
	return $registrationIDs;
}

function get_regid_details($reg_id){
	
	global $db;
	
	$query = "SELECT * FROM hanu_devices WHERE RegId='$reg_id' LIMIT 1";
	$stmt = $db->prepare($query);
	$sqlVars = array();

	if (!$stmt->execute($sqlVars)){
		$stmt = null;
		return null;
	}
	else{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt = null;
		return $row;
	}
}

function save_regid_details($app_id, $regId, $iid, $tz, $app_version,$platform){
	
	global $db;
	
	$query = "INSERT INTO hanu_devices (AppId, RegId, InstanceId, TimeZone, AppVersion,Platform,CreatedAt) VALUES ($app_id,'$regId','$iid','$tz','$app_version','$platform',current_timestamp)";
	$stmt = $db->prepare($query);
	$sqlVars = array();

	if (!$stmt->execute($sqlVars)){
		$stmt = null;
		return -1;
	}
	else{
		$id = $db->lastInsertId();
		$stmt = null;
		return $id;
	}
	
}

function deleteRegId($regId){
	
	global $db;
	
	$query = "DELETE FROM hanu_devices WHERE RegId = '$regId'";
	$stmt = $db->prepare($query);
	$sqlVars = array();
	$stmt->execute($sqlVars);
	$stmt = null;
}

?>
