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
		
?>
<!doctype html>
<html>
<head>

<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/dns_custom.css" />
<title>Direct Domain</title>
</head>

<body>
<p style="text-align:right; padding-right:50px"><a href="setacc.php">[Account Management]</a>&nbsp;<a href="udomain.php">[Personal Domain]</a>&nbsp;<a href="logout.php">[Logout]</a></p>
<div class="main">
	<h1 style="text-align:center;">Register a domain</h1>
    <h4 style="text-align:center;">net.nsysu.edu.tw</h4>
	<form class="regi" method="post">
        <p><input style="width:100px;" type="text" name="domain" placeholder="Domain name">.net.nsysu.edu.tw</p>
        <p><input style="width:110px;" type="text" name="ip" placeholder="IP Address" value="<? echo $_SERVER['REMOTE_ADDR'];?>">&nbsp;<button class="btn btn-primary">Fill in My IP</button></p>
        <p style="text-align:center;"><button class="btn btn-success" type="submit">Update domain</button></p>
    </form>
</div>
<p>&nbsp;</p>
<div class="log">
	<h3 style="text-align:center;;">Log</h3>
    <?php if(count($_POST)>0){ foreach($_POST as $k=>$v){ echo $k."=".$v."<br>"; } } ?>
	<?php
    if(file_exists("/tmp/tmp_nsupdate"))
    {
        if(unlink("/tmp/tmp_nsupdate"))
            echo "Delete tmp file.<br>";
        else
            echo "Delete tmp file error!(file not found?)<br>";
    }
    if(count($_POST["domain"]))
    {
        $file=fopen("/tmp/tmp_nsupdate","w");
        echo fprintf($file,"server 140.117.170.91\n");
        echo fprintf($file,"zone net.nsysu.edu.tw\n");
        echo fprintf($file,"update delete %s.net.nsysu.edu.tw\n",$_POST["domain"]);
        echo fprintf($file,"update add %s.net.nsysu.edu.tw 60 A %s\n",$_POST["domain"],$_POST["ip"]);
        echo fprintf($file,"show\nsend\n");
        echo "<br>Script file created.<br>";
    }
    
    if(file_exists("/tmp/tmp_nsupdate"))
    {
        $output=nl2br(shell_exec("/usr/bin/sudo /usr/bin/nsupdate -d -k /etc/bind/Knet.nsysu.+157+55142.key /tmp/tmp_nsupdate"));
        if($output)
        {
            echo "<li>".$output."</li>";
            echo "<li>Domain created.<br>";
        } else {
            echo "Something failed...<br>";
        }
    }
?>
</div>

</body>
</html>