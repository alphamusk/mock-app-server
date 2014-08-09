<?php
// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Set client parameter
appClient('localhost', '9001', $_GET['appclient'], gethostbyname(exec('hostname')));

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
		
		
		// Connect to server
		$client = stream_socket_client('tcp://'.$serverIP.':'.$serverPort, $errno, $errorMessage);
		
		if (!$client) {
			throw new Exception($errorMessage);
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
		print_r($result); 
		
		// Close client connections
		fclose($client);	

	
	} catch (Exception $e) {
		// Return error and exit
		echo 'Client Error: '.$e->getMessage()."\n";
		
		// Close the server socket
		fclose ($client);
		exit;
	}
	
	
	


	

}

?>