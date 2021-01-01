	<?php
/*

Windows Push Notification

*/
require_once("Class.PushNotification.php");

class WindowsPushNotification extends PushNotification{
	
	private $access_token = '';
	private $app_credential_data = array();

	public function __construct($regIDs){
		
		parent::__construct($regIDs); 

		$configData = getConfig();

		$this->app_credential_data = (array) $configData->wpn_credentials;
	}
		
	private function get_access_token(){

		$sid = $this->app_credential_data["$this->app_id"]["sid"];
		$secret = $this->app_credential_data["$this->app_id"]["secret"];
		
		$str = "grant_type=client_credentials&client_id=$sid&client_secret=$secret&scope=notify.windows.com";
        $url = "https://login.live.com/accesstoken.srf";
        
		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
		$output = curl_exec($ch);
        curl_close($ch);                       
        $output = json_decode($output);
        
		if(isset($output->error)){
            throw new Exception($output->error_description);
        }
		
        $this->access_token = $output->access_token;
		
    }
	
	private function postWPNMessage($uri,$xml_data,$headers){
		
		$ch = curl_init($uri);
		
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
		//echo "Headers : "; var_dump($headers);
		//echo "Body : "; var_dump($xml_data);
		
		$output = curl_exec($ch);
        $response = curl_getinfo( $ch );
        curl_close($ch);
    
        $code = $response['http_code'];
		
        if($code == 200){
            // Success
			$this->output[] = "<br>" . "Message delivered to channel : " . $uri ;
        }
		
        else if($code == 401){
			// Error in authorization code
			$this->output[] = "<br>Error in authorization code. Will get new token and try again.";
			
			// Reset authorization code.
            //$this->access_token = '';
			//$this->get_access_token();
			
			// Try again.
            //return $this->postWPNMessage($uri, $xml_data, $wpn_type);
			
			//TODO - Make sure it does not go into a loop.
        }
		
        else if($code == 410 || $code == 404){
            // Channel is expired or not valid. So delete it.
			$this->output[] = "<br>" . "Channel : " . $uri . " was expired. Will be deleted. Code: " . $code;
			$this->deleteRegId($uri);
        }
		
		else if($code == 403){
            // Channel is expired or not valid. So delete it.
			$this->output[] = "<br>" . "Combination of Channel : " . $uri . " and App credentials do not match." . 
			"Will be deleted. Code: " . $code;
			$this->deleteRegId($uri);
        }
		
        else{
            // Unknown error
			$this->output[] = "<br>" . "Error occured with error code: " . $code . " for " . $uri;
			$this->output[] = "<br> ===== <br>";
			var_dump($response);
			$this->output[] = "<br> ===== <br>";
        }
		
	}
	
	public function notifyToSync(){
		
		// Temporarily just send a toast message
		//$subject = "New jokes have been uploaded";
		//$content = "Launch the app to read new joeks.";
		//$this->sendInfoMessage($subject,$content,$message_id=0);
		
		$xml_data = "<NotificationData>
						<Task>PerformSync</Task>
					</NotificationData>";
		
		// We have all the reg Ids in an array.
		foreach($this->regIDs as $appID => $uri_list){
			
			if(count($uri_list) < 1){
				continue;
			}

			// Set current App Id
			$this->app_id = $appID;
			//var_dump($this->app_id);
			
			// Get token
			$this->access_token = '';
			$this->get_access_token();
			//var_dump($this->access_token);
			
			$headers = array(
				"Content-Type: application/octet-stream", 
				"Content-Length: " . strlen($xml_data), 
				"X-WNS-Type: wns/raw", 
				"Authorization: Bearer $this->access_token"
				);
			
			foreach($uri_list as $uri){
			
				$this->postWPNMessage($uri,$xml_data,$headers);
			
			}
			
		}
		
		//*/
	}
	
	public function notifyToSyncUsingTimeStamp($timestamp){
	
		// Temporarily just send a toast message
		//$subject = "New jokes have been uploaded";
		//$content = "Launch the app to read new joeks.";
		//$this->sendInfoMessage($subject,$content,$message_id=0);
		
		$xml_data = "<NotificationData>
						<Task>PerformSync</Task>
						<LatestDataTimeStamp>" . $timestamp . "</LatestDataTimeStamp>
					</NotificationData>";
	
		// We have all the reg Ids in an array.
		foreach($this->regIDs as $appID => $uri_list){
		
			// Set current App Id
			$this->app_id = $appID;
			//var_dump($this->app_id);
			
			// Get token
			$this->access_token = '';
			$this->get_access_token();
			//var_dump($this->access_token);
			
			$headers = array(
				"Content-Type: application/octet-stream", 
				"Content-Length: " . strlen($xml_data), 
				"X-WNS-Type: wns/raw", 
				"Authorization: Bearer $this->access_token"
				);
			
			foreach($uri_list as $uri){
			
				$this->postWPNMessage($uri,$xml_data,$headers);
			
			}
		
		}
	
	//*/
	}
	
	public function sendGenericMessage($message){
		// Must be redefined in sub classes
	}
	
	public function sendGenericMessageWithData($message,$message_data){
		// Must be redefined in sub classes
	}
	
	public function sendPing(){
				
		$xml_data = "<NotificationData><Task>PingMessage</Task></NotificationData>";
		
		// We have all the reg Ids in an array.
		foreach($this->regIDs as $appID => $uri_list){
			
			// Set current App Id
			$this->app_id = $appID;
			
			// Get token
			$this->access_token = '';
			$this->get_access_token();
			
			$headers = array(
				"Content-Type: application/octet-stream", 
				"Content-Length: " . strlen($xml_data), 
				"X-WNS-Type: wns/raw", 
				"Authorization: Bearer $this->access_token"
				);
			
			foreach($uri_list as $uri){
			
				$this->postWPNMessage($uri,$xml_data,$headers);
			
			}
			
		}
		
	}
	
	public function sendInfoMessage($subject,$content,$message_id=0){
		
		$content = "<![CDATA[" . $content . "]]>";
		
		$xml_data = "<NotificationData>
						<Task>ShowInfoMessage</Task>
						<MessageID>$message_id</MessageID>
						<Title>$subject</Title>
						<Content>$content</Content>
					</NotificationData>";
		
		// We have all the reg Ids in an array.
		foreach($this->regIDs as $appID => $uri_list){
			
			// Set current App Id
			$this->app_id = $appID;
			//var_dump($this->app_id);
			
			// Get token
			$this->access_token = '';
			$this->get_access_token();
			//var_dump($this->access_token);
			
			$headers = array(
				"Content-Type: application/octet-stream", 
				"Content-Length: " . strlen($xml_data), 
				"X-WNS-Type: wns/raw", 
				"Authorization: Bearer $this->access_token"
				);
			
			foreach($uri_list as $uri){	
				$this->postWPNMessage($uri,$xml_data,$headers);
			}		
		}
	}
		
	public function sendUserData($user,$pwd){
		// Must be redefined in sub classes
	}
	
	public function sendMessage($message,$notif_message){
		// Must be redefined in sub classes
	}
	
	public function sendMessageWithPostIds($message,$postIds){
		
	}
}

?>