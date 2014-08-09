<?php
// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Start a server
	while(true) {
		startServer('0.0.0.0', '9001', 5, 'MST');
	}
	
	
	
	
// Code for App Server
function startServer($serverIp, $bindPort, $secondsIdle, $timeZone) {
	try {
		
		
		// Set Timezone
		if(!empty($timeZone)) {
			date_default_timezone_set($timeZone);
		} else {
			throw new Exception('Timezone is invalid');
		}
		
		
		// Is the Server IP valid
		if (empty($serverIp) || !filter_var($serverIp, FILTER_VALIDATE_IP)) {
    		throw new Exception('Server IP address is invalid');
		}
		
		
		if(empty($bindPort)) {
			throw new Exception('Server binding port is invalid');
		}
		
		
		// Create and Open server socket
		$socket = stream_socket_server('tcp://'.$serverIp.':'.$bindPort, $errno, $errstr);
		
		
		// Check to ensure socket was created
		if(!$socket) {
			throw new Exception($errstr. '('.$errno.')');
		}
		
	
		// While we have a connection, do something
		while ($conn = @stream_socket_accept($socket, $secondsIdle)) {

			
			// Read client
			
			$clientRead = fread($conn, 1024);
			$clientArray = explode(':::', $clientRead);
			
			$client = $clientArray[0];
			$clientIp = $clientArray[1];
			
			
			if (!empty($client)) {
				
				$clientName = $_SERVER['REMOTE_ADDR'];
				
				
				// Run client param
				$result = exec($client, $output, $retval);
				
				
				// Respond back to client
				if($retval == 0) {
					fwrite($conn, $result);					
				} else {
					fwrite($conn, 'Server Error: Command "'.$client.'" not valid.'."\n");
					throw new Exception('Unknown command "'.$client.'" from client '.$clientIp);
					
				}
				

				
				// Log 
				echo "Success\t".date('Y-m-d'."\t\G\M\T". 'O g:i:s e')."\t".exec('hostname').': "'.$client.'" from client '.$clientIp."\n";
				
				// Close the client connection
				fclose($conn);				
				
				
			} else {
				
				
				// Close the client connection
				fclose($conn);
				throw new Exception('Null client request');
				
				
			}

		}
		
		
	} catch (Exception$e) {
		
		// Return error and exit
		echo "Error\t".date('Y-m-d'."\t\G\M\T". 'O g:i:s e')."\t".exec('hostname').': '.$e->getMessage()."\n";
	
	}
	
}


?>