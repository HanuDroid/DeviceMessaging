<?php
/*

Google Push Notification

*/
require_once("Class.PushNotification.php");
require_once("GCMFunctions.php");

class GooglePushNotification extends PushNotification{
	
	private $token_list = array();

	public function __construct($regIDs){
		
		parent::__construct($regIDs); 

		$configData = getConfig();

		$this->token_list = (array) $configData->gcm_tokens;
	}
	
	public function notifyToSync(){
		
		$now = new DateTime();
		$this->notifyToSyncUsingTimeStamp($now->format('Y-m-d H:i:s'));

	}
	
	public function notifyToSyncUsingTimeStamp($timestamp){
		
		// This is to send a simple sync message
		$message = "PerformSync";
		
		foreach($this->regIDs as $appID => $reg_id_list){
			
			$this->app_id = $appID;
			
			$reg_id_bundles = array_chunk($reg_id_list, 990);
			
			foreach($reg_id_bundles as $reg_id_bundle){
				
				$fields = array(
					'registration_ids'  => $reg_id_bundle,
					'data'              => array( "message" => $message, "latest_data_timestamp" => $timestamp ),
					"time_to_live" => 43200
					);
					
				// Return result
				$this->postGCMMessage($fields);
				
			}
			
		}
		
	}
	
	public function sendPing(){
		
		// This is to send a simple sync message
		$this->sendGenericMessage("PingMessage","");

	}
	
	public function sendGenericMessage($message){
		
		// This is to send a generic message		
		foreach($this->regIDs as $appID => $reg_id_list){
			
			$this->app_id = $appID;
			
			$reg_id_bundles = array_chunk($reg_id_list, 990);
			
			foreach($reg_id_bundles as $reg_id_bundle){
				
				$fields = array(
					'registration_ids'  => $reg_id_bundle,
					'data'              => array( "message" => $message ),
					"time_to_live" => 43200
					);
					
				// Return result
				$this->postGCMMessage($fields);
				
			}
			
		}

	}
	
	public function sendGenericMessageWithData($message,$message_data){
		
		$gcm_message = array("message" => $message);
		$gcm_data = array_merge($gcm_message, $message_data);
		
		// This is to send a generic message		
		foreach($this->regIDs as $appID => $reg_id_list){
			
			$this->app_id = $appID;
			
			$reg_id_bundles = array_chunk($reg_id_list, 990);
			
			foreach($reg_id_bundles as $reg_id_bundle){
				
				$fields = array(
					'registration_ids'  => $reg_id_bundle,
					'data'              => $gcm_data,
					"time_to_live" => 43200
					);
					
				// Return result
				$this->postGCMMessage($fields);
				
			}
			
		}

	}
	
	public function sendUserData($user,$pwd){
		
		foreach($this->regIDs as $appID => $reg_id_list){
			
			$this->app_id = $appID;
			
			$reg_id_bundles = array_chunk($reg_id_list, 990);
			
			foreach($reg_id_bundles as $reg_id_bundle){
				
				$fields = array(
					'registration_ids'  => $reg_id_bundle,
					'data'              => array( "message" => "UserData", "user" => $user, "pwd" => $pwd ),
					"time_to_live" => 43200
					);
					
				// Return result
				$this->postGCMMessage($fields);
				
			}
			
		}
		
	}
	
	protected function processResult($reg_ids){		

		$this->output[] = "<hr />";
		$this->output[] = "===== Result of FCM for App ID: $this->app_id ====";
		$this->output[] = "<hr />";
		
		$json_array = json_decode($this->result,true);
		
		// Result Summary.
		$success = $json_array['success'] + $json_array['canonical_ids'];
		$failure = $json_array['failure'];
		
		// Result Overview.
		$this->output[] = "Success = ".$json_array['success']."<br>";
		$this->output[] = "Failure = ".$json_array['failure']."<br>";
		$this->output[] = "Canonical Ids = ".$json_array['canonical_ids']."<br>";
		
		$resultIndex = 0;
		foreach($json_array['results'] as $result){
		
			$messageId = $result['message_id'];
			$new_reg_id = $result['registration_id'];
			$error_code = $result['error'];
		
			if(strcmp($messageId,"") != 0){
				// Success :)
				//echo "Message successfully sent for Id: ".$reg_ids[$resultIndex]."<br>";
			}
		
			if(strcmp($new_reg_id,"") != 0){
				// Replace Id :-|
				$this->output[] = "Reg Id: ".$reg_ids[$resultIndex]." will be replaced by ".$new_reg_id."<br>";
				$this->replaceRegId($reg_ids[$resultIndex], $new_reg_id);
				$this->output[] = "<hr />";
			}
		
			if(strcmp($error_code,"") != 0){
				if(strcmp($error_code,"NotRegistered") == 0){
					// In-Valid ID :(
					$this->deleteRegId($reg_ids[$resultIndex]);
					$this->output[] = "Reg Id: ".$reg_ids[$resultIndex]." will be deleted<br>";
					$this->output[] = "<hr />";
				}
				elseif(strcmp($error_code,"Unavailable") == 0){
					// Un-Available :(
					$this->output[] = "Could not deliver to Id: ".$reg_ids[$resultIndex]."<br>";
					$this->output[] = "<hr />";
				}
				else{
					// Don't Know !
					$this->output[] = "Some error occured while delivering to Reg Id: ".$reg_ids[$resultIndex]."<br>";
					$this->output[] = "<hr />";
				}
			}
		
			$resultIndex++;
		}
		
		
		$summary = array('success' => $success, 'failure' => $failure);
		$this->summary_output[] = json_encode($summary);
		
	}
	
	public function sendInfoMessage($subject,$content,$message_id=0){
		
		//var_dump($this->regIDs);
		foreach($this->regIDs as $appID => $reg_id_list){
			
			$this->app_id = $appID;
			
			$reg_id_bundles = array_chunk($reg_id_list, 990);
			
			foreach($reg_id_bundles as $reg_id_bundle){
				
				$fields = array(
					'registration_ids'  => $reg_id_bundle,
					'data'	=> array( 	"message" => "InfoMessage", 
													"subject" => $subject, 
													"content" => $content,
													"message_id" => $message_id),
					"time_to_live" => 43200
					);
					
				// Return result
				$this->postGCMMessage($fields);
				
			}
			
		}
		
	}
	
	public function sendMessage($message,$notif_message){
		
		foreach($this->regIDs as $appID => $reg_id_list){
			
			$this->app_id = $appID;
			
			$reg_id_bundles = array_chunk($reg_id_list, 990);
			
			foreach($reg_id_bundles as $reg_id_bundle){
				
				$fields = array(
					'registration_ids'  => $reg_id_bundle,
					'data'              => array( "message" => $message, "notif_message" => $notif_message ),
					"time_to_live" => 43200
					);
					
				// Return result
				$this->postGCMMessage($fields);
				
			}
			
		}
	}
	
	public function sendMessageWithPostIds($message,$postIds){
		
		foreach($this->regIDs as $appID => $reg_id_list){
			
			$this->app_id = $appID;
			
			$reg_id_bundles = array_chunk($reg_id_list, 990);
			
			foreach($reg_id_bundles as $reg_id_bundle){
				
				$fields = array(
					'registration_ids'  => $reg_id_bundle,
					'data'              => array( "message" => $message, "PostIds" => $postIds ),
					"time_to_live" => 43200
					);
					
				// Return result
				//var_dump($fields);
				$this->postGCMMessage($fields);
				
			}
			
		}
	}
	
	private function postGCMMessage($fields){
		
		// Reg IDs in this message.
		$reg_ids = $fields["registration_ids"];
		
		// My Server API Key
		$apiKey = $this->token_list["$this->app_id"];
		//var_dump($apiKey);

		// Set POST variables
		$url = 'https://fcm.googleapis.com/fcm/send';

		$headers = array(
						'Authorization: key=' . $apiKey,
						'Content-Type: application/json'
						);

		// Open connection
		$ch = curl_init();
		
		//var_dump( $fields );
		
		// Set the url, number of POST vars, POST data
		curl_setopt( $ch, CURLOPT_URL, $url );

		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
		
		// Execute post
		$this->result = curl_exec($ch);
		
		//var_dump($this->result);
		
		// Close connection
		curl_close($ch);
		
		// Process Result
		$this->processResult($reg_ids);

	}

}

?>