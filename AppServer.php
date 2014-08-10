

<?php
// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);

	// Start a server
	while(true) {
		startAppServer('0.0.0.0', '9001', 5, 'MST');
	}
	
	
	
// Code for App Server
function startAppServer($serverIp, $bindPort, $secondsIdle, $timeZone) {
	try {
		
		
		// Check and set Timezone
		if(!empty($timeZone)) {
			date_default_timezone_set($timeZone);
		} else {
			throw new Exception('Timezone is invalid');
		}
		
		
		// Check for a valid Server IP
		if (empty($serverIp) || !filter_var($serverIp, FILTER_VALIDATE_IP)) {
    		throw new Exception('Server IP address is invalid');
		}
		
		
		// Check for bind port
		if(empty($bindPort)) {
			throw new Exception('Server binding port is invalid');
		}
		
		
		// Create and Open server socket
		$socket = stream_socket_server('tcp://'.$serverIp.':'.$bindPort, $errno, $errorMessage);
		
		
		// Check to ensure socket was created
		if(!$socket) {
			throw new Exception($errorMessage. '('.$errno.')');
		}
		
	
		// While we have a connection, do something
		while ($conn = @stream_socket_accept($socket, $secondsIdle)) {

			
			// Read client
			$clientRead = fread($conn, 1024);
			
			
			// Cut up what client sent
			$clientArray = explode(':::', $clientRead);
			
			
			// Client's option sent
			$client = $clientArray[0];
			
			
			// Client's IP address
			$clientIp = $clientArray[1];
			
			
			// Check to make sure client connected
			if (!empty($client)) {
				
				
				// Define Server IP via PHP
				$myName = exec('hostname');
				
				
				// Calc sqrt of random num
				$randomNum = rand(0, 99999999999999);
				$squareRoot = sqrt($randomNum);
				
				
				// Respond back to client
				if($client == 'sqrt') {
			 		fwrite($conn, 
			 				'<div id="serverResponse">'.
			 				'<p>App Server <span>'.$myName.'</span><br/>'.
			 				'The Square Root of '.$randomNum.' is '.$squareRoot.'</p>'.
			 				'</div>');	
					
				} else {
					// Send error back to client
					fwrite($conn, 'Server Error: Option "'.$client.'" not valid.'."\n");
					
					// Log error if we have client IP, else log alternate error
					if (!empty($clientIp)) {
						throw new Exception('Unknown option "'.$client.'" from client '.$clientIp);
					} else {
						throw new Exception('Unknown option "'.$client.'" from unknown client');
					}
				}

				
				// Log success
				echo "Success\t".date('Y-m-d'."\t\G\M\T". 'O g:i:s e')."\t".exec('hostname').': "'.$client.'" from client '.$clientIp."\n";
				
				// Close the client connection
				fclose($conn);				
				
				
			} else {
				
				
				// Close the client connection and send null client request
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