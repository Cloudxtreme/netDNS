<?php
	require('dmapi.php');
	require('db.php');
	session_start();
	permissionCheck($_SESSION['auth']);	
	try {
		$usercheck=$db->query('SELECT * FROM user_list WHERE name =\''.$_SESSION['user'].'\'');
		$usercheck_arr=$usercheck->fetchAll();
	}
	catch (PDOException $e)
    {
	    $err = 'Database operation failed!<br>' . $e->getMessage () . '<br>';
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
    
</div>
<p>&nbsp; </p>
<div class="log">
	<h3 style="text-align:center;">Action Log</h3>
	<?php
	try {
		$hostnamecheck=$db->query('SELECT * FROM user_list WHERE hostname =\''.$_POST["hostname"].'\'');
		$hostnamecheck_arr=$hostnamecheck->fetchAll();
		
		/*If the field "hostname" change and not blank*/
		if($_POST['hostname']!="" && $_POST['hostname']!=$usercheck_arr[0]['hostname']) {
			if(doubleDomainCheck($_POST['hostname']))
			{
				/*Check user password */
				if(md5($_POST['oldpassword'])==$usercheck_arr[0]['password']) {
					updatePersonalDomain_DB($usercheck_arr[0]['id'],$_POST['hostname']);
					$_SESSION['oldhostname']=$usercheck_arr[0]['hostname'];	
				}
			}
		}
		/*If user set a newpassword*/
		if(isset($_POST['newpassword']) && $_POST['newpassword']!="") {
			/*If both the password field is the same*/
			if($_POST['newpassword']==$_POST['cm_newpassword']) {
				updateUserPass($usercheck_arr[0]['id'],$_POST['oldpassword'],$_POST['newpassword']);
			} else {
				$err="Password not match!";
			}
		}
	}
	catch (PDOException $e)
    {
	    $err = 'Database operation failed!<br>' . $e->getMessage () . '<br>';
	    $db = null;
    }
	 if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php } 
	clearActionTmp($_SESSION['user']);
	
	$data[0]['hostname']=$_SESSION['oldhostname'];
	createDelTmp($_SESSION['user'],$data);
	unset($_SESSION['oldhostname']);
    
	execDNSaction($_SESSION['user'],"deleted");
?>
</div>
</body>
</html>