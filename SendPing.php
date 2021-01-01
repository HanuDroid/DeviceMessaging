<?php

require 'GCMFunctions.php';
require_once("Class.GooglePushNotification.php");
require_once("Class.WindowsPushNotification.php");

createDBConnection();

$blogURL = $_POST['blogurl'];

// Start Logging
$file_name = __DIR__ . "/log/" . date("Y-n-j") . ".log";

$log = "========== Ping Started ========== " . PHP_EOL;
file_put_contents($file_name, $log, FILE_APPEND);

$log = date('Y/m/d H:i:s') . " : Blog URL is: " . $blogURL . PHP_EOL;
file_put_contents($file_name, $log, FILE_APPEND);

// See if this Blog URL is registered.
$app_details = get_application_details_by_blog_url($blogURL);
if($app_details == null){
	die("Application not registered with Hanu-Droid");
}

// Get all registered devices
$app_id = $app_details['ID'];

$registrationIDs = get_registered_devices_for_app($app_id);

// For Google
$log = date('Y/m/d H:i:s') . " : Sending Ping Message to Google IDs " . PHP_EOL;
file_put_contents($file_name, $log, FILE_APPEND);

$GCM_IDs = $registrationIDs['Google'];
	
$gcm_notifications = new GooglePushNotification($GCM_IDs);
$gcm_notifications->sendPing();
$gcm_notifications->printResult();
echo "Done google";
echo "<hr />";
echo "<hr />";	

$log = date('Y/m/d H:i:s') . " : Ping message sent to all Google IDs " . PHP_EOL;
file_put_contents($file_name, $log, FILE_APPEND);

// For Windows
$log = date('Y/m/d H:i:s') . " : Sending Ping Message to Windows IDs " . PHP_EOL;
file_put_contents($file_name, $log, FILE_APPEND);

$WPN_IDs = $registrationIDs['Windows'];	

if($WPN_IDs != null)
{	

	try{
		
		// No need to break it in chunks because here we send one by one.
		$wpn_notifications = new WindowsPushNotification($WPN_IDs);						
		$wpn_notifications->sendPing();			
		$wpn_notifications->printResult();						
		echo "<hr />";			
		echo "<hr />";
	
	} catch (Exception $e) {
		echo "<hr />";
		echo "Exception in WPN";		
		echo "<hr />";
	}
	
}

$log = date('Y/m/d H:i:s') . " : Ping message sent to all Windows IDs " . PHP_EOL;
file_put_contents($file_name, $log, FILE_APPEND);

//*/
$log = "========== Ping Finished ========== " . PHP_EOL;
file_put_contents($file_name, $log, FILE_APPEND);
?>