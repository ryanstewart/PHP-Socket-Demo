<?php

create_connection('localhost',1740);

function create_connection($host,$port)
{
	$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	
	if (!is_resource($socket)) {
		echo 'Unable to create socket: '. socket_strerror(socket_last_error()) . PHP_EOL;
	} else {
		echo "Socket created.\n";
	}
	
	if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
	    echo 'Unable to set option on socket: '. socket_strerror(socket_last_error()) . PHP_EOL;
	} else {
		echo "Set options on socket.\n";
	}
	
	if (!socket_bind($socket, $host, $port)) {
	    echo 'Unable to bind socket: '. socket_strerror(socket_last_error()) . PHP_EOL;
	} else {
		echo "Socket bound to port $port.\n";
	}
	
	if (!socket_listen($socket,SOMAXCONN)) {
		echo 'Unable to listen on socket: ' . socket_strerror(socket_last_error());
	} else {
		echo "Listening on the socket.\n";
	}
	
	while (true)
	{
		$connection = @socket_accept($socket);
		
		if($connection)
		{		
			echo "Client $connection connected!\n";
			send_data($connection);

		} else {
			echo "Bad connection.";
		}
	}
}

function send_data($connection)
{
	echo $connection;
	// Create a number between 30 and 32 that will be our initial stock price.
	$stock_price = rand(30,32);
	while (true)
	{
		socket_write($connection,"$stock_price\n",strlen("$stock_price\n"));
		sleep(2);
		
		// Generate a random number that will represent how much our stock price
		// will change and then make that number a decimal and attach it to the 
		// previous price.
		$stock_offset = rand(-50,50);
		$stock_price = $stock_price + ($stock_offset/100);
		echo "$stock_price\n";
	}
}


?>