<?php
	require('dmapi.php');
	require('db.php');
	session_start();
	permissionCheck($_SESSION['auth']);
	permissionCheck($_SESSION['admin']);
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
    <p>&nbsp;</p>
    <?php /*======================== Action Log Block (DB action)========================*/ ?>
    <div class="main" style="height:280px;float:right; position:relative; top:820px; overflow:auto; <?php /*if(isset($err) || isset($hint)) { ?> opacity:1; <?php } else { ?> opacity:0.8; <?php }*/ ?> ">
        <h1 style="text-align:center;">Action Log</h1>
        <?php
			clearActionTmp($_SESSION['user']);
			try {
				$pubdomain=$db->query('SELECT * FROM pub_domain');
				$pubdomain_arr=$pubdomain->fetchAll();
				
				if(isset($_POST['pub_host']))
				{
					addPublicDomain_DB($_POST['pub_creator'],$_POST['pub_type'],$_POST['pub_host'],$_POST['pub_ip']);
				}
				
				if(isset($_POST['name']))
				{
					addUser($_POST['name'],$_POST['password'],$_POST['hostname'],$_POST['fullname']);
				}
				
				$acc_list=$db->query("SELECT * FROM user_list");
				$acc_list_arr=$acc_list->fetchAll();
				$pd_list=$db->query("SELECT * FROM pub_domain");
				$pd_list_arr=$pd_list->fetchAll();
				
				$db = null;
			}
			catch (PDOException $e)
			{
				$err = 'Database operation failed!<br>' . $e->getMessage () . '<br>';
				$db = null;
			}
			if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php }
			
			if($_POST['pub_type']=='A') //Type A record
				createUpdateTmp($_SESSION['user'],$new_pd_arr);
			else if($_POST['pub_type']=='C') //Type CNAME record
				createCnameTmp($_SESSION['user'],$new_pd_arr);
				  
			execDNSaction($_SESSION['user'],"created");
		?>
    </div>
    
    
    <?php /*======================== Create Account Block ========================*/ ?>
    <div class="main" style="float:left;">
        <h1 style="text-align:center;">Create Account</h1>
        <h4 style="text-align:center;">DDNS Service</h4>
        <form class="regi" method="post" id="ddnsacc">
            <span style="color:#F00;">* means the required field.</span>
            <table class="table">
                <tr>
                    <td style="text-align:right;"><span style="color:#F00;">*</span>Username:</td>
                    <td><input style="width:100px;" type="text" name="name" placeholder="[Max char.:20]" required></td>
                </tr>
                <tr>
                    <td style="text-align:right;"><span style="color:#F00;">*</span>Full name:</td>
                    <td><input style="width:100px;" type="text" name="fullname" placeholder="[Max char.:20]" required></td>
                </tr>
                <tr>
                    <td style="text-align:right;"><span style="color:#F00;">*</span>Password:</td>
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
        <?php /*if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php }*/ ?>
    </div>
    
    <?php /*======================== Public Domain Block ========================*/ ?>
    <div class="main" style="height:780px;float:right; position:relative; top:-310px; overflow:auto;">
        <h1 style="text-align:center;">Public Domain</h1>
        
        <form method="post" id="upddom" action="upddom.php">
        <table class="table table-bordered">
            <tr>
                <td>ID</td>
                <td>Creator</td>
                <td>Type</td>
                <td>Hostname</td>
                <td>IP / Alias</td>
                <td>Update</td>
                <td>Delete</td>
            </tr>
    <?php foreach($pd_list_arr as $row) { ?>
            <tr>
                <td style="text-align:center;"><?php echo $row['id']; ?><input name="id" type="hidden" value="<?php echo $row['id']; ?>"></td>
                <td style="text-align:center;"><?php echo $row['creator']; ?></td>
                <td style="text-align:center;"><?php echo $row['type']; ?></td>
                <td style="text-align:center;"><?php echo $row['hostname']; ?></td>         
                <td style="text-align:center;"><input style="width:110px" name="ip" type="text" value="<?php echo $row['ip']; ?>"></td>
                <td style="text-align:center;"><input class="btn btn-warning" type="submit" value="Go!" onclick="if(confirm('Do you really want to update with new IP?')) return true;else return false"></td>
                <td style="text-align:center;"><a href="deldom.php?id=<?php echo $row['id']; ?>"><img alt="remove" style="width:30px;" src="img/del.png" onclick="if(confirm('Do you really want to remove this domain?')) return true;else return false"></a></td>
            </tr>
    <?php } ?>
        </table>
        </form>
        <h4 style="text-align:center;">Add one immediately</h4>
        <form class="regi" method="post" id="pubdomain">
        	<input name="pub_type" type="hidden" value="A">
            <table class="table">
            	<caption>A & CNAME record only</caption>
                <tr>
                    <td style="text-align:right;">Hostname</td>
                    <td><input style="width:100px;" type="text" name="pub_host" placeholder="Domain name" required>.net.nsysu.edu.tw</td>
                </tr>
             	<tr>
                    <td style="text-align:right;">Type</td>
                    <td><select style="width:50px;" name="pub_type" size="1"><option>A</option><option>C</option></select>&nbsp;&nbsp;(A = A Record, C = CNAME Record)</td>
                </tr>
                <tr>
                    <td style="text-align:right;">IP/Alias</td>
                    <td><input style="width:120px;" type="text" name="pub_ip" placeholder="IP Address or Alias" value="<?php //echo $_SERVER['REMOTE_ADDR'];?>" required></td>
                </tr>
                <tr><td colspan="2" style="text-align:center;"><input name="pub_creator" type="hidden" value="<?php echo $_SESSION['user']; ?>"><button class="btn btn-primary" type="submit" onclick="if(confirm('Is the information correct?')) return true;else return false">Add</button></td></tr>
            </table>
        </form>
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
                <td style="text-align:center;"><a href="rstAccPass.php?id=<?php echo $row['id']; ?>"><img alt="PSreset" style="width:30px;" src="img/reset.png" onclick="if(confirm('Do you really want to reset user password to 1234?')) return true;else return false"></a></td>
                <td style="text-align:center;"><?php echo $row['hostname']; ?></td>
                <td style="text-align:center;">
                    <?php if($row['admin']==1) { 
    ?>				<img alt="Admin" src="img/adm.png" style="width:30px;">
    <?php 			if($_SESSION['user']!=$row['name']) { ?>
                        &nbsp;<a href="authadm.php?id=<?php echo $row['id']; ?>&act=0"><img alt="RmAdm" style="width:30px;" src="img/ban.png"></a>
    <?php				} } else {?><a href="authadm.php?id=<?php echo $row['id']; ?>&act=1"><img alt="AddAdm" style="width:30px;" src="img/key.png"></a><?php } ?>
                </td>
                <td style="text-align:center;">
                    <?php if($row['name']!=$_SESSION['user']) {
    ?>            	<a href="delacc.php?id=<?php echo $row['id']; ?>"><img alt="remove" style="width:30px;" src="img/del.png" onclick="if(confirm('Do you really want to remove this user?')) return true;else return false"></a><?php } ?>
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