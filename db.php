<?php
	$dsn="mysql:host=localhost;dbname=dns_daemon";
	$user='ddns';
	$password='f5018ddns';
	// Create (connect to) SQLite database in file
	$db=new PDO ($dsn,$user,$password);
	
	// Set errormode to exceptions
	$db->setAttribute(PDO::ATTR_ERRMODE,
							PDO::ERRMODE_EXCEPTION);
?>