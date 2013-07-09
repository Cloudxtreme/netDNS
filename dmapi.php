<?php

function permissionCheck($session)
{
	if($session!=true)
	{
		header('Location: permissiondeny.php');
		exit;
	}
}

function doubleDomainCheck($host)
{
	$hostnamecheck=$GLOBALS['db']->query('SELECT * FROM user_list WHERE hostname =\''.$host.'\'');
	$hostnamecheck_arr=$hostnamecheck->fetchAll();
	$pubhostnamecheck=$GLOBALS['db']->query("SELECT * FROM pub_domain WHERE hostname ='".$host."'");
	$pubhostnamecheck_arr=$pubhostnamecheck->fetchAll();
	/*Check if the domain is exist in public domain */
	if(count($pubhostnamecheck_arr)==0)
	{
		/*Check if the hostname is registered or not*/
		if(count($hostnamecheck_arr)==0)
		{
			return 1;
		}
	}
}

function addUser($name,$pass,$host,$fullname)
{
	$usercheck=$GLOBALS['db']->query('SELECT * FROM user_list WHERE name =\''.$name.'\'');
	$usercheck_arr=$usercheck->fetchAll();
	/*Check if the user is exist or not */
	if(count($usercheck_arr)==0)
	{
		/*Check if the hostname is registered or not*/
		if(doubleDomainCheck($host))
		{
			// Prepare INSERT statement to db
			$insert = "INSERT INTO user_list (name,password,hostname,fullname)
							VALUES (:name,:password,:hostname,:fullname)";
							
			$stmt = $GLOBALS['db']->prepare ($insert);
			
			//Bind parameter to variable
			$stmt->bindParam (':name'		, $name );
			$password=md5($pass);
			$stmt->bindParam (':password'   , $password );
			if(strlen($host)!=0 && $host!=NULL) {
				$stmt->bindParam (':hostname' , $host );
			} else {
				$host="";
				$stmt->bindParam (':hostname' , $host );
			}
			$stmt->bindParam (':fullname'		, $fullname );
				
			// Execute statement
			if ($stmt->execute() == FALSE) {
				$err = "Create account failed.";
			}	else {
				$hint="Create account successful!";
			}
		} else {
			$err = "The hostname was already registered.";
		}
	} else {
		$err = "The user was already exist.";
	}
	if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint; ?></p><?php } 
	if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php }
}

function addPublicDomain_DB($creator,$type,$host,$ip) //Pass domain information
{
	//Check if the hostname is registered or not
	if(doubleDomainCheck($host))
	{
		// Prepare INSERT statement to db
		$insert = "INSERT INTO pub_domain (creator,type,hostname,ip)
						VALUES (:name,:type,:hostname,:ip)";
						
		$stmt = $GLOBALS['db']->prepare ($insert);
		
			//Bind parameter to variable
			$stmt->bindParam (':name'		, $creator );
			$stmt->bindParam (':type'		, $type );
			$stmt->bindParam (':hostname' 	, $host );
			$stmt->bindParam (':ip' 		, $ip );
				
			// Execute statement
			if ($stmt->execute() == FALSE) {
				$err = "Add domain error.";
			} else {
				$hint="Add domain successful!";
				global $runaction;
				$runaction=1;
			}
	} else {
		$err = "The hostname was already registered.";
	}
	$new_pd=$GLOBALS['db']->query('SELECT * FROM pub_domain WHERE hostname =\''.$host.'\'');
	global $new_pd_arr;
	$new_pd_arr=$new_pd->fetchAll();
	if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint; ?></p><?php } 
	if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php }
}

function delPublicDomain_DB($id) //Pass domain ID
{
	$domaincheck=$GLOBALS['db']->query('SELECT * FROM pub_domain WHERE id =\''.$id.'\'');
	global $domaincheck_arr;
	$domaincheck_arr=$domaincheck->fetchAll();
	
	if(count($domaincheck_arr)==1)
	{
		// Prepare Delete statement
		$delete = "DELETE FROM pub_domain WHERE id = :id";
						
		$stmt = $GLOBALS['db']->prepare ($delete);
		
		//Bind parameter to variable
		$stmt->bindParam (':id', $id );
			
		// Execute statement
		if ($stmt->execute() == FALSE) {
			$err="Delete domain Error!";
		} else {
			$hint="Delete domain successful!";
			global $runaction;
			$runaction=1;
		}
	} else {
		$err="No domain found!";
	}
	if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint; ?></p><?php } 
	if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php }
}

function updatePublicDomain_DB($id,$ip)
{
	$update = "UPDATE pub_domain SET ip = :ip WHERE id = :id";
	$stmt = $GLOBALS['db']->prepare($update);
			
	$stmt->bindParam(':ip', $ip);
	$stmt->bindParam(':id', $id);
	
	if($stmt->execute()==FALSE) {
		$err="Something wrong with PDO operation. =(";
	}
	global $runaction;
	$runaction=1;
}

function clearActionTmp($user)
{
	if(file_exists("/tmp/tmp_nsupdate_".$user))
    {
        if(unlink("/tmp/tmp_nsupdate_".$user))
            $hint = "Delete tmp file.<br>";
        else
            $err = "Delete tmp file error!(file not found?)<br>";
    }
	if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint; ?></p><?php } 
	if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php }
}

function createDelTmp($user,$data)
{
	global $runaction;
	if($runaction==1)
    {
        $file=fopen("/tmp/tmp_nsupdate_".$user,"w");
        fprintf($file,"server net.nsysu.edu.tw\n");
        fprintf($file,"zone net.nsysu.edu.tw\n");
        fprintf($file,"update delete %s.net.nsysu.edu.tw\n",$data[0]['hostname']);
        fprintf($file,"send\n");
        $hint = "Delete script file created.";
    }
	if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><?php echo $hint; ?></p><?php }
}

function createUpdateTmp($user,$data)
{
	if($GLOBALS['runaction']==1)
    {
        $file=fopen("/tmp/tmp_nsupdate_".$user,"w");
        fprintf($file,"server net.nsysu.edu.tw\n");
        fprintf($file,"zone net.nsysu.edu.tw\n");
        fprintf($file,"update delete %s.net.nsysu.edu.tw\n",$data[0]['hostname']);
		fprintf($file,"update add %s.net.nsysu.edu.tw 604800 A %s\n",$data[0]['hostname'],$data[0]['ip']);
        fprintf($file,"send\n");
        $hint = "Update script file created.";
    }
	if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><?php echo $hint; ?></p><?php }
}

function createCnameTmp($user,$data)
{
	if($GLOBALS['runaction']==1)
    {
        $file=fopen("/tmp/tmp_nsupdate_".$user,"w");
        fprintf($file,"server net.nsysu.edu.tw\n");
        fprintf($file,"zone net.nsysu.edu.tw\n");
        fprintf($file,"update delete %s.net.nsysu.edu.tw cname\n",$data[0]['hostname']);
		fprintf($file,"update add %s.net.nsysu.edu.tw 600 cname %s.net.nsysu.edu.tw\n",$data[0]['hostname'],$data[0]['ip']);
        fprintf($file,"send\n");
        $hint = "CNAME script file created.";
    }
	if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><?php echo $hint; ?></p><?php }
}

function execDNSaction($user,$action) //Pass $_SESSION['user'] & action string
{
	if(file_exists("/tmp/tmp_nsupdate_".$user))
    {
        $output=nl2br(shell_exec("/usr/bin/sudo /usr/bin/nsupdate -d -k /etc/bind/Knet.nsysu.+157+55142.key /tmp/tmp_nsupdate_".$user));
        if($output)
        {
            //echo "<li>".$output."</li>";
            $hint = "Domain ".$action.".";
        } else {
            $err = "Something failed...(Key not found? Exec permission?)";
        }
    }
	if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint; ?></p><?php }
	if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php }
}



?>