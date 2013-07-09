<?php
	require('db.php');
	session_start();
	if($_SESSION['auth']==true)
	{
		if($_SESSION['admin']==true)
		{
			header('Location: admpanel.php');
			exit;
		}
		else
		{
			header('Location: udomain.php');
			exit;
		}
	}
		
	try {	
		if(isset($_POST['name']))
		{
			$usercheck=$db->query('SELECT * FROM user_list WHERE name =\''.$_POST["name"].'\'');
			$usercheck_arr=$usercheck->fetchAll();
			
			if(count($usercheck_arr)==1)
			{
				if($usercheck_arr[0]['name']==$_POST['name'] && $usercheck_arr[0]['password']==md5($_POST['password']))
				{
					session_start();
					
					$_SESSION['auth']=true;
					$_SESSION['user']=$_POST['name'];
					if($usercheck_arr[0]['admin']==1)
					{
						$_SESSION['admin']=true;
						header('Location: admpanel.php');
						exit;
					}
					else {
						$_SESSION['admin']=false;
						header('Location: udomain.php');
						exit;
					}
				}
				else
					$err='Sorry, maybe your username or password is incorrect!';
			} else {
				$err='Sorry, maybe your username or password is incorrect!';
			}
		}
		$acc_list=$db->query("SELECT * FROM user_list");
		$acc_list_arr=$acc_list->fetchAll();
		
		$db = null;
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
<title>DDNS Service :: Login</title>
</head>

<body>
<div class="main">
	<h1 style="text-align:center;">DNS Service - User Login</h1>
    <h4 style="text-align:center;">net.nsysu.edu.tw</h4>
	<form class="regi" method="post" action="login.php">
    	<table class="table">
            <tr>
                <td style="text-align:right;">Username:</td>
                <td><input style="width:100px;" type="text" name="name" required></td>
            </tr>
            <tr>
                <td style="text-align:right;">Password:</td>
                <td><input style="width:110px;" type="password" name="password" required></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;"><button class="btn btn-primary" type="submit">Login</button></td>
            </tr>
        </table>
    </form>
    <?php if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="50" src="img/sad.png"> &nbsp; <?php echo $err; ?></p><?php } ?>
</div>

<p>&nbsp;</p>

</body>
</html>