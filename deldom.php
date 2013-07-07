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
		
		if(isset($_GET['id']))
		{
			$domaincheck=$db->query('SELECT * FROM pub_domain WHERE id =\''.$_GET["id"].'\'');
			$domaincheck_arr=$domaincheck->fetchAll();
			
			if(count($domaincheck_arr)==1)
			{
				// Prepare Delete statement
				$delete = "DELETE FROM pub_domain WHERE id = :id";
								
				$stmt = $db->prepare ($delete);
				
				//Bind parameter to variable
				$stmt->bindParam (':id', $_GET['id'] );
					
				// Execute statement
				if ($stmt->execute() == FALSE) {
					$err="Delete domain Error!";
				} else {
					$hint="Delete domain successful!";
				}
			} else {
				$err="No domain found!";
			}
		} else {
			$err="Oops! Something wrong!";
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
<title>Delete Domain</title>
</style>
</head>

<body>
<p style="text-align:right; padding-right:50px"><a href="admpanel.php">[Admin Panel]</a>&nbsp;<a href="udomain.php">[Personal Domain]</a>&nbsp;<a href="logout.php">[Logout]</a></p>
<div class="main">
	<h1 style="text-align:center;">Delete Public Domain</h1>
    <h4 style="text-align:center;">net.nsysu.edu.tw</h4>
    <?php if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php } ?>
    <?php if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint; ?></p><?php } ?>
</div>
<p>&nbsp;</p>
<div class="log">
	<h3 style="text-align:center;;">Action Log</h3>
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
	
    if(count($usercheck_arr)==1)
    {
        $file=fopen("/tmp/tmp_nsupdate_".$_SESSION['user'],"w");
        fprintf($file,"server net.nsysu.edu.tw\n");
        fprintf($file,"zone net.nsysu.edu.tw\n");
        fprintf($file,"update delete %s.net.nsysu.edu.tw\n",$domaincheck_arr[0]['hostname']);
        fprintf($file,"send\n");
        $hint2 = "Script file created.";
    }
	if(isset($hint2)) { ?><p style="text-align:center; color:#3C0;"><?php echo $hint2; ?></p><?php }
    
    if(file_exists("/tmp/tmp_nsupdate_".$_SESSION['user']))
    {
        $output=nl2br(shell_exec("/usr/bin/sudo /usr/bin/nsupdate -d -k /etc/bind/Knet.nsysu.+157+55142.key /tmp/tmp_nsupdate_".$_SESSION['user']));
        if($output)
        {
            //echo "<li>".$output."</li>";
            $hint3 = "Domain deleted.";
        } else {
            $err3 = "Something failed...";
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
    <?php var_dump($_GET); ?>
     <?php var_dump($usercheck_arr); ?>
	
</div>
*/ ?>

</body>
</html>
<script language="javascript">
var speed=3000;
setTimeout("goto()",speed);

function goto() {
	location = "admpanel.php";
}
</script>