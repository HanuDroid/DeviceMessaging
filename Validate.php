<?php
require( 'GCMFunctions.php' );

$blog_url = $_POST["blogurl"];

if($blog_url == "https://apps.ayansh.com/Hindi-Jokes"){
	echo "Success";
}
else if($blog_url == "https://hanu-droid.varunverma.org/Applications/DesiJokes"){
	echo "Success";
}
else{
	// Create DB Connection
	createDBConnection();

	// Get application details
	$app_details = get_application_details_by_blog_url($blog_url);

	if($app_details == null){
		echo "Not Found";
	}
	else{
		echo "Success";
	}
}

?>