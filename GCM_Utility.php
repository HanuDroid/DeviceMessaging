<?php
require_once( 'GCMFunctions.php' );
require_once("Class.GooglePushNotification.php");
require_once("Class.WindowsPushNotification.php");

$code = $_POST["code"];

createDBConnection();

if($code == "sync_notification_to_user")
{	
	$reg_id = $_POST["gcm_token"];	
	
	$reg_id_array = array();	
	$reg_id_array[] = $reg_id;	
	$reg_id_details = get_regid_details($reg_id);
	$app_id = 	$reg_id_details['AppID'];
	
	if($reg_id_details['Platform'] == 'Android')
	{		$pushNotificationService = new GooglePushNotification(array("$app_id" => $reg_id_array));	}	

	else if($reg_id_details['Platform'] == 'Windows' || $reg_id_details['Platform'] == 'WindowsPhone')
	{		$pushNotificationService = new WindowsPushNotification(array("$app_id" => $reg_id_array));	}	

	else
	{		echo "Could not determine the platform";	}		

	$pushNotificationService->notifyToSync();	
	$pushNotificationService->printResult();
	
}

if($code == "sync_notification_to_iid")
{	
	$iid = $_POST["iid"];
	
	$registrationIDs = get_reg_ids_for_iid($iid);
	
	// For Google	
	$GCM_IDs = $registrationIDs['Google'];
	if($GCM_IDs != null)
	{	
		// No need to break it in chunks because here we send one by one.
		$gcm_notifications = new GooglePushNotification($GCM_IDs);						
		$gcm_notifications->notifyToSync();	
		$gcm_notifications->printResult();			
		echo "<hr />";			
		echo "<hr />";
	}
	
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
		
}

if($code == "notification_to_user" || $code == "notification_to_blog_users" || $code == "notification_to_all_users" || $code == "notification_to_instance_ids")
{	
	if($code == "notification_to_user")
	{				
		$reg_id = $_POST["gcm_token"];		
		$reg_id_array = array();		
		$reg_id_array[] = $reg_id;		
		$reg_id_details = get_regid_details($reg_id);	
		$app_id = 	$reg_id_details['AppID'];		
		
		if($reg_id_details['Platform'] == 'Android')
		{			$registrationIDs = array('Google' 	=> array("$app_id" => $reg_id_array));		}		
	
		else if($reg_id_details['Platform'] == 'Windows' || $reg_id_details['Platform'] == 'WindowsPhone')
		{			$registrationIDs = array('Windows' 	=> array("$app_id" => $reg_id_array));		}		
	
		else
		{			$registrationIDs = array('Apple' 	=> array("$app_id" => $reg_id_array));		}			
	
	}		
	
	else if ($code == "notification_to_blog_users")
	{			
		$blog_url = $_POST['blog_url'];			
		$app_details = get_application_details_by_blog_url($blog_url);		
		
		if($app_details == null)
		{			
			echo "Blog not registered with Hanu GCM Framework";			
			return;		
		}		
		
		// Get all registered devices		
		$app_id = $app_details['ID'];		
		$registrationIDs = get_registered_devices_for_app($app_id);			
		
	}	
	
	else if($code == "notification_to_all_users") 
	{				
		// Get all registered devices		
		$registrationIDs = get_all_registered_devices();			
	}	
	
	else if($code == "notification_to_instance_ids") 
	{				
		// Get all registered devices for Instance ID	
		$iid = $_POST['iid'];
		//echo $iid;
		$registrationIDs = get_reg_ids_for_iid($iid);
		//var_dump($registrationIDs);
	}	
	
	else
	{		
		return;	
	}	
	
	$subject = $_POST['subject'];	
	$content = $_POST['content'];	
	$mid = $_POST['mid'];		
	
	// For Google	
	$GCM_IDs = $registrationIDs['Google'];
	if($GCM_IDs != null)
	{	
		// No need to break it in chunks because here we send one by one.
		$gcm_notifications = new GooglePushNotification($GCM_IDs);						
		$gcm_notifications->sendInfoMessage($subject,$content,$mid);
		$gcm_notifications->printResult();			
		echo "<hr />";			
		echo "<hr />";
	}
		
	// For Windows	
	$WPN_IDs = $registrationIDs['Windows'];	
	//var_dump($WPN_IDs);
	
	if($WPN_IDs != null)
	{	
		// No need to break it in chunks because here we send one by one.
		$wpn_notifications = new WindowsPushNotification($WPN_IDs);						
		$wpn_notifications->sendInfoMessage($subject,$content,$mid);			
		$wpn_notifications->printResult();						
		echo "<hr />";			
		echo "<hr />";
	}	
	
}
?>