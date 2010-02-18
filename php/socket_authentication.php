<?php
/*
	Copyright (c) 2010 Ryan Stewart
	http://blog.digitalbackcountry.com

	=============================================================================
	"THE BEER-WARE LICENSE" (Revision 42):
	<ryan@ryanstewart.net> wrote this file. As long as you retain this notice you
	can do whatever you want with this stuff. If we meet some day, and you think
	this stuff is worth it, you can buy me a beer in return. =Ryan Stewart
	=============================================================================

	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without
	restriction, including without limitation the rights to use,
	copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following
	conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	OTHER DEALINGS IN THE SOFTWARE.
*/
	
	// Get the flashpolicy.xml file so we can send it to the Flash Player
	$filename = "./flashpolicy.xml";
	$content = file_get_contents($filename);
	
	// Create a socket that uses the IPv4 protocol over TCP. By changin the
	// parameters passed into the function we could create an IPv6 socket and/or 
	// create a socet that would use UDP.
	$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	
	if (!is_resource($socket)) {
		echo 'Unable to create socket: '. socket_strerror(socket_last_error()) . PHP_EOL;
	} else {
		echo "Socket created.\n";
	}
	
	//  Next we bind to a specfic host and port. In this case, the port is 843 because 
	// we're listening for the Flash Player's policy file request.
	if (!socket_bind($socket, 'localhost', 843)) {
	    echo 'Unable to bind socket: '. socket_strerror(socket_last_error()) . PHP_EOL;
	} else {
		echo "Socket bound to port 843.\n";
	}
	
	// Once we successfully bind to the host and port, we can start listening for requests
	// from the client.
	if (!socket_listen($socket,SOMAXCONN)) {
		echo 'Unable to listen on socket: ' . socket_strerror(socket_last_error());
	} else {
		echo "Listening on the socket.\n";
	}
	
	// Unlike a typical PHP page which runs and then finishes, we want to always be 
	// looking for new connections. So we use an infinite loop that will accept connections
	// and then handle them.
	while(true)
	{
		
		// Create a connection by accepting the client's attempt to connect to the socket.
		$connection = @socket_accept($socket);	
		if ($connection)
		{
			echo "Client $connection connected!\n";		
		} else
		{
			echo "Bad connection.";
		}
	
		// Read the data the client is sending and set it as the input variable. 
		// If that variable is a policy file request then we serve up the policy 
		// file. 
		$input = socket_read($connection,1024);
		echo $input."\n";
		
		if( $input == "<policy-file-request/>\0")
		{
			echo "Policy file request\n";
		} else
		{
			echo "Unknown request\n";
			socket_close($connection);
			break;
		}
		
		// Send the data from the policy file to the client by writing it to the 
		// socket. 
		socket_write($connection,$content,strlen($content));
		socket_write($connection,"\0",strlen("\0"));
		socket_close($connection);
		
	}


?>