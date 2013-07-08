<?php

require('db.php');

function addPublicDomain_DB($creator,$type,$host,$ip)
{
	$hostnamecheck=$db->query('SELECT * FROM user_list WHERE hostname =\''.$_POST["pub_host"].'\'');
	$hostnamecheck_arr=$hostnamecheck->fetchAll();
	$pubhostnamecheck=$db->query("SELECT * FROM pub_domain WHERE hostname ='".$_POST['pub_host']."'");
	$pubhostnamecheck_arr=$pubhostnamecheck->fetchAll();
	/*Check if the domain is exist in public domain */
	if(count($pubhostnamecheck_arr)==0)
	{
		/*Check if the hostname is registered or not*/
		if(count($hostnamecheck_arr)==0)
		{
			// Prepare INSERT statement to db
			$insert = "INSERT INTO pub_domain (creator,type,hostname,ip)
							VALUES (:name,:type,:hostname,:ip)";
							
			$stmt = $db->prepare ($insert);
			
				//Bind parameter to variable
				$stmt->bindParam (':name'		, $creator );
				$stmt->bindParam (':type'		, $type );
				$stmt->bindParam (':hostname' 	, $host );
				$stmt->bindParam (':ip' 		, $ip );
					
				// Execute statement
				if ($stmt->execute() == FALSE) {
?>
				<script>
					window.alert ("Add Domain ERROR!");
				</script>
<?php
				}
				$rundomain=1;
		} else {
			$err2 = "The hostname was already registered.";
		}
	} else {
		$err2 = "The hostname was already registered.";
	}
}

function delPublicDomain_DB($id)
{
	$domaincheck=$db->query('SELECT * FROM pub_domain WHERE id =\''.$_GET["id"].'\'');
	$domaincheck_arr=$domaincheck->fetchAll();
	
	if(count($domaincheck_arr)==1)
	{
		// Prepare Delete statement
		$delete = "DELETE FROM pub_domain WHERE id = :id";
						
		$stmt = $db->prepare ($delete);
		
		//Bind parameter to variable
		$stmt->bindParam (':id', $id );
			
		// Execute statement
		if ($stmt->execute() == FALSE) {
			$err="Delete domain Error!";
		} else {
			$hint="Delete domain successful!";
		}
	} else {
		$err="No domain found!";
	}
}
?>