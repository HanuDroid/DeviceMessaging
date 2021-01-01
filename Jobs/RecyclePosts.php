<!--
    Copyright 2019  Varun Verma  (email : support@ayansh.com)

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

require_once("../GCMFunctions.php");

$blogList = array(  "HJ" => "https://apps.ayansh.com/Hindi-Jokes",
                    "DJ" => "https://hanu-droid.varunverma.org/Applications/DesiJokes",
                    "SS" => "https://apps.ayansh.com/Swag-Status",
                    "HS" => "https://apps.ayansh.com/Hindi-Shayari");

$tags = getTagsByDate();
//var_dump($tags);

foreach($blogList as $key => $blogURL){

    echo "===================================== \n";
    echo "Processing for ".$key . "\n";

    $url = $blogURL . "/Post-Utility/Recycle_Posts_Job.php";
    $post_data = "tag_list=" . $tags;

    $headers = array();
    $post_data = array();

    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt( $ch, CURLOPT_URL, $url );

    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    //curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data);
    
    // Execute post
    $result = curl_exec($ch);
    
    echo $result;
    //var_dump($result);
    
    // Close connection
    curl_close($ch);

}

function getTagsByDate(){

    $configData = getConfig();

	$host = $configData->db->host;
	$user = $configData->db->user;
	$pass = $configData->db->password;
    $database = $configData->db->apps_dbname;
    
    $tags = "";
    
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $pass);

    $query = "SELECT * FROM `calendar_events` WHERE `FromDate` <= CURRENT_DATE and `ToDate` >= CURRENT_DATE AND`Active` = 'X' order by `EventName` LIMIT 1";

    $stmt = $db->prepare($query);
    $sqlVars = array();

    if (!$stmt->execute($sqlVars)){
    }
    else{
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $tags = $row['Tags'];
    }
    
    $stmt = null;

    return $tags;
}

?>