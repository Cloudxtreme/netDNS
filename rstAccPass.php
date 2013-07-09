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
<title>Reset Account Password</title>
</style>
</head>

<body>
<p style="text-align:right; padding-right:50px"><a href="admpanel.php">[Admin Panel]</a>&nbsp;<a href="udomain.php">[Personal Domain]</a>&nbsp;<a href="logout.php">[Logout]</a></p>
<div class="main">
	<h1 style="text-align:center;">Reset Account Password</h1>
    <h4 style="text-align:center;">net.nsysu.edu.tw</h4>
    <?php if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php } ?>
    <?php if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint; ?></p><?php } ?>
</div>
<p>&nbsp;</p>
<div class="log">
	<h3 style="text-align:center;">Action Log</h3>
	<?php
	try {
		if(isset($_GET['id']))
		{
			resetUserPass($_GET['id']);
		} else {
			$err="Oops! Something wrong!";
		}
		$db = null;
	}
	catch (PDOException $e)
    {
	    $err = 'Database operation failed!<br>' . $e->getMessage () . '<br>';
	    $db = null;
    }
	if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php }
?>
</div>
</body>
</html>
<script language="javascript">
var speed=5000;
setTimeout("goto()",speed);

function goto() {
	location = "admpanel.php";
}
</script>