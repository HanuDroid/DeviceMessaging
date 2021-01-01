<!--
	This is called by the Hanu Droid WP Plugin
-->
<?php

require 'GCMFunctions.php';
require_once("Class.GooglePushNotification.php");
require_once("Class.WindowsPushNotification.php");

createDBConnection();

$blogURL = $_POST['blogurl'];
$message = $_POST['message'];
$notif_message = $_POST['notif_message'];

if( strcmp($message,"") == 0){
	$message = "PerformSync";
}

// See if this Blog URL is registered.
$app_details = get_application_details_by_blog_url($blogURL);
if($app_details == null){
	die("Application not registered with Hanu-Droid");
}

// Get all registered devices
$app_id = $app_details['ID'];
$registrationIDs = get_registered_devices_for_app($app_id);

// For Google
$GCM_IDs = $registrationIDs['Google'];	
$gcm_notifications = new GooglePushNotification($GCM_IDs);

$gcm_notifications->notifyToSync();
$gcm_notifications->printResult();

echo "<hr />";
echo "<hr />";

// For Windows
$WPN_IDs = $registrationIDs['Windows'];	
//var_dump($WPN_IDs);

if($WPN_IDs != null)
{	
	// No need to break it in chunks because here we send one by one.
	$wpn_notifications = new WindowsPushNotification($WPN_IDs);						
	$wpn_notifications->notifyToSync();			
	$wpn_notifications->printResult();						
	echo "<hr />";			
	echo "<hr />";
}

// For Apple - Whenever I can afford to buy Mac.

?>