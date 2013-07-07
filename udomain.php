<?php
	session_start();
	if($_SESSION['auth']!=true)
	{
		header('Location: permissiondeny.php');
		exit;
	}
	
	try {
		include('db.php');
		
		if(isset($_SESSION['user']))
		{
			$usercheck=$db->query('SELECT * FROM user_list WHERE name =\''.$_SESSION['user'].'\'');
			$usercheck_arr=$usercheck->fetchAll();
			$hostnamecheck=$db->query('SELECT * FROM user_list WHERE hostname =\''.$_POST["hostname"].'\'');
			$hostnamecheck_arr=$hostnamecheck->fetchAll();
			
			if(count($usercheck_arr)==0)
			{
				$err="Something wrong! :(((";
			}	
		}
		
		/*If the field "hostname" change and not blank*/
		if($_POST['hostname']!="" && $_POST['hostname']!=$usercheck_arr[0]['hostname']) {
			if(count($hostnamecheck_arr)==0)
			{
				/*Check user password */
				if(md5($_POST['oldpassword'])==$usercheck_arr[0]['password']) {
					$update = "UPDATE user_list SET hostname =  :hostname WHERE id = :id;";
					$stmt = $db->prepare($update);
					
					$stmt->bindParam(':hostname', $_POST['hostname']);
					$stmt->bindParam(':id', $usercheck_arr[0]['id']);
					
					if($stmt->execute()==FALSE) {
						$err="Something wrong with PDO operation. =(";
					}
					/*$update = "UPDATE user_list SET fullname =  :fullname WHERE id = :id;";
					$stmt = $db->prepare($update);
					
					$stmt->bindParam(':fullname', $_POST['fullname']);
					$stmt->bindParam(':id', $usercheck_arr[0]['id']);
					
					if($stmt->execute()==FALSE) {
						$err="Something wrong with PDO operation. =(";
					}*/
					$rmdomain=1;
					$_SESSION['oldhostname']=$usercheck_arr[0]['hostname'];
					$hint="Hostname successful change! <br> Don't forget to do \"sudo make clean\" and restart the daemon!";
				}
			} else {
				$err = "The hostname was already registered.";
			}
		}
		
		/*If user set a newpassword*/
		if(isset($_POST['newpassword']) && $_POST['newpassword']!="") {
			/*If both the password field is the same*/
			if($_POST['newpassword']==$_POST['cm_newpassword']) {
				/*If the password match the password in db.*/
				if(md5($_POST['oldpassword'])==$usercheck_arr[0]['password']) {
					$update = "UPDATE user_list SET password =  :password WHERE id = :id;";
					$stmt = $db->prepare($update);
					
					$stmt->bindParam(':password', md5($_POST['newpassword']));
					$stmt->bindParam(':id', $usercheck_arr[0]['id']);
					
					if($stmt->execute()==FALSE) {
						$err="Something wrong with PDO operation. =(";
					}
					
					$hint4="Password successful change!";
				} else { 
					$err4="Password incorrect!";
				}
			} else {
				$err4="Password not match!";
			}
		}
		
		//Refesh User Data
		if(isset($_SESSION['user']))
		{
			$usercheck=$db->query('SELECT * FROM user_list WHERE name =\''.$_SESSION['user'].'\'');
			$usercheck_arr=$usercheck->fetchAll();
			
			if(count($usercheck_arr)==0)
			{
				$err="Something wrong! :(((";
			}	
		}
		
		$db = null;
	}
	catch (PDOException $e)
    {
	    echo 'DB operation failed!<br>' . $e->getMessage () . '<br>';
	    $db = null;
    }
	
?>
<!doctype html>
<html>
<head>

<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/dns_custom.css" />
<title>Personal Domain Management</title>
</head>

<body>
    <p style="text-align:right; padding-right:50px">
		<?php if($_SESSION['admin']==true) {?><a href="admpanel.php">[Admin Panel]</a><?php } ?>&nbsp;
        <a href="daemon.php">[DDNS Daemon]</a> &nbsp;
        <a href="logout.php">[Logout]</a>
   	</p>
<div class="main">
	<h1 style="text-align:center;">Personal Domain Management</h1>
    <h4 style="text-align:center;">net.nsysu.edu.tw</h4>
	<form class="regi" method="post" action="udomain.php">
    	<font color="#FF0000">* means the required field.</font>
    	<table class="table">
        	<tr>
            	<td style="text-align:right;">Username:</td>
                <td><?php echo $usercheck_arr[0]['name']; ?></td>
            </tr>
            <tr>
            	<td style="text-align:right;">Full name:</td>
                <td><!--<input style="width:100px;" type="text" name="fullname" placeholder="[Max char.:20]" required>--><?php echo $usercheck_arr[0]['fullname']; ?></td>
            </tr>
            <tr>
            	<td style="text-align:right;"><font color="#FF0000">*</font>Password:</td>
                <td><input style="width:110px;" type="password" name="oldpassword" placeholder="[Max char.:30]" required></td>
            </tr>
            <tr>
            	<td style="text-align:right;">New Password:</td>
                <td><input style="width:110px;" type="password" name="newpassword" placeholder="[Max char.:30]"></td>
            </tr>
            <tr>
            	<td style="text-align:right;">Confirm your password:</td>
                <td><input style="width:110px;" type="password" name="cm_newpassword" placeholder="[Max char.:30]"></td>
            </tr>
            <tr>
            	<td style="text-align:right;"><font color="#FF0000">*</font>Hostname:</td>
                <td><input style="width:100px;" type="text" name="hostname" placeholder="[Max char.:20]" value="<?php echo $usercheck_arr[0]['hostname'];?>" required></td>
            </tr>
            <tr>
            	<td colspan="2" style="text-align:center;"><button class="btn btn-success" type="submit">Modify!</button></td>
            </tr>
		</table>
    </form>
    <?php if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php } ?>
    <?php if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint; ?></p><?php } ?>
    <?php if(isset($hint4)) { ?><p style="text-align:center; color:#F90;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint4; ?></p><?php } ?>
    <?php if(isset($err4)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err4; ?></p><?php } ?>
</div>
<p>&nbsp;  </p>
<div class="log">
	<h3 style="text-align:center;">Action Log</h3>
	<?php
    if(file_exists("/tmp/tmp_nsupdate_".$_SESSION['user']))
    {
        if(unlink("/tmp/tmp_nsupdate_".$_SESSION['user']))
            $hint1 = "Delete tmp file.<br>";
        else
            $err1 = "Delete tmp file error!(file not found?)<br>";
    }
	if(isset($hint1)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint1; ?></p><?php } 
	if(isset($err1)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err1; ?></p><?php }
	
    if($rmdomain==1)
    {
        $file=fopen("/tmp/tmp_nsupdate_".$_SESSION['user'],"w");
        fprintf($file,"server net.nsysu.edu.tw\n");
        fprintf($file,"zone net.nsysu.edu.tw\n");
        fprintf($file,"update delete %s.net.nsysu.edu.tw\n",$_SESSION['oldhostname']);
        fprintf($file,"send\n");
        $hint2 = "Script file created.";
		unset($_SESSION['oldhostname']);
    }
	if(isset($hint2)) { ?><p style="text-align:center; color:#3C0;"><?php echo $hint2; ?></p><?php }
    
    if(file_exists("/tmp/tmp_nsupdate_".$_SESSION['user']))
    {
        $output=nl2br(shell_exec("/usr/bin/sudo /usr/bin/nsupdate -d -k /etc/bind/Knet.nsysu.+157+55142.key /tmp/tmp_nsupdate_".$_SESSION['user']));
        if($output)
        {
            //echo "<li>".$output."</li>";
            $hint3 = "Old domain deleted.";
        } else {
            $err3 = "Something failed?...";
        }
    }
	if(isset($hint3)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint3; ?></p><?php }
	if(isset($err3)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err3; ?></p><?php }
?>
</div>
<?php /*
<p>&nbsp;</p>
<div class="log">
	<h3 style="text-align:center;">Debug Log</h3>
    <?php if(count($_POST)>0){ foreach($_POST as $k=>$v){ echo $k."=".$v."<br>"; } } ?>
    <?php var_dump($usercheck_arr); ?>
    <?php var_dump($_SESSION); ?>
	
</div>
*/ ?>
</body>
</html>