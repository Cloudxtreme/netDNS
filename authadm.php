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
	
	if($_GET['act']==1)
		$act=1;
	else if($_GET['act']==0)
		$act=0;
	else
		$act=99;
			
	try {
		include('db.php');
		
		if(isset($_GET['id'])&&$act!=99)
		{
			$usercheck=$db->query('SELECT * FROM user_list WHERE id =\''.$_GET["id"].'\'');
			$usercheck_arr=$usercheck->fetchAll();
			
			if(count($usercheck_arr)==1)
			{
				// Prepare Delete statement
				$update = "UPDATE user_list SET admin = :act WHERE id = :id";
								
				$stmt = $db->prepare ($update);
				
				//Bind parameter to variable
				$stmt->bindParam (':id', $_GET['id'] );
				$stmt->bindParam (':act', $act );
					
				// Execute statement
				if ($stmt->execute() == FALSE) {
					$err="Permission change Error!";
				} else {
					$hint="Permission change successful!";
				}
			} else {
				$err="No user found!";
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
<title>Delete Domain Acc.</title>
</style>
</head>

<body>
<p style="text-align:right; padding-right:50px"><a href="domain.php">[Direct Domain]</a>&nbsp;<a href="udomain.php">[Personal Domain]</a>&nbsp;<a href="logout.php">[Logout]</a></p>
<div class="main">
	<h1 style="text-align:center;">Auth Admin</h1>
    <h4 style="text-align:center;">net.nsysu.edu.tw</h4>
    <?php if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php } ?>
    <?php if(isset($hint)) { ?><p style="text-align:center; color:#3C0;"><img width="50" src="img/good.png"> &nbsp; <?php echo $hint; ?></p><?php } ?>
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
var speed=2000;
setTimeout("goto()",speed);

function goto() {
	location = "admpanel.php";
}
</script>