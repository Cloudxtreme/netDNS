<?php
	session_start();
	if($_SESSION['auth']!=true)
	{
		header('Location: permissiondeny.php');
		exit;
	}
	if($_SESSION['admin']!=true)
	{
		header('Location: permissiondeny.php');
		exit;
	}
	try {
		include('db.php');
		
		$pubdomain=$db->query('SELECT * FROM pub_domain');
		$pubdomain_arr=$pubdomain->fetchAll();
		
		if(isset($_POST['pub_host']))
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
					$insert = "INSERT INTO pub_domain (creator,hostname,ip)
									VALUES (:name,:hostname,:ip)";
									
					$stmt = $db->prepare ($insert);
					
						//Bind parameter to variable
						$stmt->bindParam (':name'		, $_POST['pub_creator'] );
						$stmt->bindParam (':hostname' 	, $_POST['pub_host'] );
						$stmt->bindParam (':ip' 		, $_POST['pub_ip'] );
							
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
		
		if(isset($_POST['name']))
		{
			$usercheck=$db->query('SELECT * FROM user_list WHERE name =\''.$_POST["name"].'\'');
			$usercheck_arr=$usercheck->fetchAll();
			$hostnamecheck=$db->query('SELECT * FROM user_list WHERE hostname =\''.$_POST["hostname"].'\'');
			$hostnamecheck_arr=$hostnamecheck->fetchAll();
			
			/*Check if the user is exist or not */
			if(count($usercheck_arr)==0)
			{
				/*Check if the hostname is registered or not*/
				if(count($hostnamecheck_arr)==0)
				{
					// Prepare INSERT statement to db
					$insert = "INSERT INTO user_list (name,password,hostname,fullname)
									VALUES (:name,:password,:hostname,:fullname)";
									
					$stmt = $db->prepare ($insert);
					
						//Bind parameter to variable
						$stmt->bindParam (':name'		, $_POST['name'] );
						$stmt->bindParam (':password'   , md5($_POST['password']) );
						if(isset($_POST['hostname']))
							$stmt->bindParam (':hostname' , $_POST['hostname'] );
						else
							$stmt->bindParam (':hostname' , '');
						$stmt->bindParam (':fullname'		, $_POST['fullname'] );
							
						// Execute statement
						if ($stmt->execute() == FALSE) {
		?>
						<script>
							window.alert ("Create Account ERROR!");
						</script>
<?php
						}
				} else {
					$err = "The hostname was already registered.";
				}
			} else {
				$err = "The user was already exist.";
			}
		}
		$acc_list=$db->query("SELECT * FROM user_list");
		$acc_list_arr=$acc_list->fetchAll();
		
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
<title>Admin Panel</title>
<style>

</style>
</head>

<body>
<div class="bblock">
    <p style="text-align:right; padding-right:50px;color:#F5F5F5;">Login:&nbsp;<?php echo $_SESSION['user'];?>&nbsp;&nbsp;<a href="udomain.php">[Personal Domain]</a>&nbsp;<a href="logout.php">[Logout]</a></p>
	<h1 style="text-align:center;color:#F5F5F5;">DNS Management Panel</h1>
    <h4 style="text-align:center;color:#F5F5F5;">net.nsysu.edu.tw</h4>
    <?php /*======================== Public Domain Block ========================*/ ?>
    <div class="main" style="float:right;">
        <h1 style="text-align:center;">Public Domain</h1>
        
        <table class="table table-bordered">
            <tr>
                <td>ID</td>
                <td>Creator</td>
                 <td>Hostname</td>
                 <td>IP</td>
                 <td>Delete</td>
            </tr>
    <?php foreach($pubdomain_arr as $row) { ?>
            <tr>
                <td style="text-align:center;"><?php echo $row['id']; ?></td>
                <td style="text-align:center;"><?php echo $row['creator']; ?></td>
                <td style="text-align:center;"><?php echo $row['hostname']; ?></td>         
                <td style="text-align:center;"><?php echo $row['ip']; ?></td>
                <td style="text-align:center;"><a href="deldom.php?id=<?php echo $row['id']; ?>"><img width="30" src="img/del.png" onclick="if(confirm('Do you really want to remove this domain?')) return true;else return false"></a></td>
            </tr>
    <?php } ?>
        </table>
        <h4 style="text-align:center;">Add one immediately</h4>
        <form class="regi" method="post" id="pubdomain">
            <table class="table">
                <tr>
                    <td style="text-align:right;">Hostname</td>
                    <td><input style="width:100px;" type="text" name="pub_host" placeholder="Domain name" required>.net.nsysu.edu.tw</td>
                </tr>
                <tr>
                    <td style="text-align:right;">IP</td>
                    <td><input style="width:110px;" type="text" name="pub_ip" placeholder="IP Address" value="<?php //echo $_SERVER['REMOTE_ADDR'];?>" required></td>
                </tr>
                <tr><td colspan="2" style="text-align:center;"><input name="pub_creator" type="hidden" value="<?php echo $_SESSION['user']; ?>"><button class="btn btn-primary" type="submit">Add</button></td></tr>
            </table>
        </form>
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
        
        if($rundomain==1)
        {		
            /*Renew DNS List */
            $file=fopen("/tmp/tmp_nsupdate_".$_SESSION['user'],"w");
            fprintf($file,"server net.nsysu.edu.tw\n");
            fprintf($file,"zone net.nsysu.edu.tw\n");
            fprintf($file,"update delete %s.net.nsysu.edu.tw\n",$_POST['pub_host']);
            fprintf($file,"update add %s.net.nsysu.edu.tw 604800 A %s\n",$_POST["pub_host"],$_POST["pub_ip"]);
            fprintf($file,"send\n");
            $hint2 = "Script file created.";
        }
        if(isset($err2)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err2; ?></p><?php }
        if(isset($hint2)) { ?><p style="text-align:center; color:#3C0;"><?php echo $hint2; ?></p><?php }
        
        if(file_exists("/tmp/tmp_nsupdate_".$_SESSION['user']))
        {
            $output=nl2br(shell_exec("/usr/bin/sudo /usr/bin/nsupdate -d -k /etc/bind/Knet.nsysu.+157+55142.key /tmp/tmp_nsupdate_".$_SESSION['user']));
            if($output)
            {
                //echo "<li>".$output."</li>";
                $hint3 = "Domain created.";
            } else {
                $err3 = "Something failed?...";
            }
        }
        if(isset($hint3)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint3; ?></p><?php }
        if(isset($err3)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err3; ?></p><?php }
    ?>
    </div>
    <?php /*======================== Create Account Block ========================*/ ?>
    <div class="main" style="float:left;">
        <h1 style="text-align:center;">Create Account</h1>
        <h4 style="text-align:center;">DDNS Service</h4>
        <form class="regi" method="post" id="ddnsacc">
        	<font color="#FF0000">* means the required field.</font>
            <table class="table">
                <tr>
                    <td style="text-align:right;"><font color="#FF0000">*</font>Username:</td>
                    <td><input style="width:100px;" type="text" name="name" placeholder="[Max char.:20]" required></td>
                </tr>
                <tr>
                    <td style="text-align:right;"><font color="#FF0000">*</font>Full name:</td>
                    <td><input style="width:100px;" type="text" name="fullname" placeholder="[Max char.:20]" required></td>
                </tr>
                <tr>
                    <td style="text-align:right;"><font color="#FF0000">*</font>Password:</td>
                    <td><input style="width:110px;" type="password" name="password" placeholder="[Max char.:30]" required></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Hostname:</td>
                    <td><input style="width:100px;" type="text" name="hostname" placeholder="[Max char.:20]"> <br>（留空表示由用戶自行設定）</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;"><button class="btn btn-success" type="submit">Create!</button></td>
                </tr>
            </table>
        </form>
        <?php if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php } ?>
    </div>
    <?php /*======================== Management Account Block ========================*/ ?>
    <div class="main" style="float:left;">
        <h1 style="text-align:center;">Account Management</h1>
        <h4 style="text-align:center;">DDNS Service</h4>
        <table class="table table-bordered">
            <tr>
                <td>ID</td>
                <td>Username</td>
                <td>Full name</td>
                <td>Ps. Reset</td>
                 <td>Hostname</td>
                 <td>Admin</td>
                 <td>Delete</td>
            </tr>
    <?php foreach($acc_list_arr as $row) { ?>
            <tr>
                <td style="text-align:center;"><?php echo $row['id']; ?></td>
                <td style="text-align:center;"><?php echo $row['name']; ?></td>
                <td style="text-align:center;"><?php echo $row['fullname']; ?></td>
                <td style="text-align:center;"><img width="30" src="img/reset.png"></td>
                <td style="text-align:center;"><?php echo $row['hostname']; ?></td>
                <td style="text-align:center;">
                    <?php if($row['admin']==1) { 
    ?>				<img width="30" src="img/adm.png">
    <?php 			if($_SESSION['user']!=$row['name']) { ?>
                        &nbsp;<a href="authadm.php?id=<?php echo $row['id']; ?>&act=0"><img width="30" src="img/ban.png"></a>
    <?php				} } else {?><a href="authadm.php?id=<?php echo $row['id']; ?>&act=1"><img width="30" src="img/key.png"></a><?php } ?>
                </td>
                <td style="text-align:center;">
                    <?php if($row['name']!=$_SESSION['user']) {
    ?>            	<a href="delacc.php?id=<?php echo $row['id']; ?>"><img width="30" src="img/del.png" onclick="if(confirm('Do you really want to remove this user?')) return true;else return false"></a><?php } ?>
                </td>
            </tr>
    <?php } ?>
        </table>
    </div>
</div>

<?php /*
<div class="log">
	<h3 style="text-align:center;">Debug Log</h3>
    <?php if(count($_POST)>0){ foreach($_POST as $k=>$v){ echo $k."=".$v."<br>"; } } ?>
    <pre>
    <?php echo "SESSION:<br>"; var_dump($_SESSION); echo "ARR:<br>"; var_dump($hostnamecheck_arr);var_dump($pubhostnamecheck_arr); ?>
    </pre>
	
</div>
*/ ?>
</body>
</html>