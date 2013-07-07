<?php
		$err='You don\'t have permission for the request page<br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		We\'ll take you to the login page in 3 seconds.';
?>
<!doctype html>
<html>
<head>

<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="css/dns_custom.css" />
<title>Create Domain Acc. :: Login</title>
</head>

<body>
<div class="main">
	<h1 style="text-align:center;">Permission denied</h1>
    <?php if(isset($err)) { ?><p style="text-align:center; color:#F00;"><img width="80" src="img/ban.png"> &nbsp; <?php echo $err; ?></p><?php } ?>
</div>

</body>
</html>
<script language="javascript">
var speed=3000;
setTimeout("goto()",speed);

function goto() {
	location = "login.php";
}
</script>