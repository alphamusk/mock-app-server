<?php

// Code for app client
function appClient($serverName, $serverPort, $param, $clientIp) {
	try {
		
		// Translate hostname to IP
		if(!empty($serverName)) {
			$serverIP = gethostbyname($serverName);
		} else {
			throw new Exception('Servername no set');
		}
		
		
		// Check for server port
		if(empty($serverPort)) {
			throw new Exception('Server port not set');
		}
		
		
		// Connect to app server
		$client = stream_socket_client('tcp://'.$serverIP.':'.$serverPort, $errno, $errorMessage);
		
		if (!$client) {
			throw new Exception($errorMessage. '('.$errno.')');
		}

		
		// Check client param
		if(!empty($param)) {
	 		fwrite($client, $param.':::'.$clientIp);
		} else {		
			throw new Exception('appclient parameter set to null');
		}		
	

		// Output the results from the server
		$result =  stream_get_contents($client);
		
		// Return results
		echo $result; 
		
		// Close client connections
		fclose($client);	

	
	} catch (Exception $e) {
		// Return error and exit
		echo 'Client Error: '.$e->getMessage()."\n";
		
		// Close the server socket
		fclose ($client);
	}
	
}

?>