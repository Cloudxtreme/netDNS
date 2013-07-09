<?php
	$dsn="mysql:host=net.nsysu.edu.tw;dbname=dns_daemon";
	$user='ddns';
	$password='f5018ddns';
	// Create (connect to) MySQL connection
	$db=new PDO ($dsn,$user,$password);
	
	// Set errormode to exceptions
	$db->setAttribute(PDO::ATTR_ERRMODE,
							PDO::ERRMODE_EXCEPTION);
?>