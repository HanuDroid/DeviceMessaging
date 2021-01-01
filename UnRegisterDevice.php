<?php

require( 'GCMFunctions.php' );

// Create DB Connection
createDBConnection();

$package = $_POST[package];
$regId = $_POST[regid];

// Get application details
$app_details = get_application_details($package);

if($app_details == null){
	die("Application not registered with Hanu-Droid");
}

// Check if entry already exists.
$reg_id_details = get_regid_details($regId);

if($reg_id_details == null){
	// Nothing to do
}
else{
	// Delete
	deleteRegId($regId);
}

echo "Success";

?>