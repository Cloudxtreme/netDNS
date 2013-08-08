<?php
	session_start();
	if($_SESSION['auth']!=true)
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
<title>DDNS Daemon</title>
</head>

<body>
	<p style="text-align:right; padding-right:50px;color:#F5F5F5;">
    	Login:&nbsp;<?php echo $_SESSION['user'];?>&nbsp;&nbsp;
		<?php if($_SESSION['admin']==true) {?><a href="admpanel.php">[Admin Panel]</a><?php } ?>&nbsp;
        <a href="udomain.php">[Personal Domain]</a> &nbsp;
        <a href="logout.php">[Logout]</a>
   	</p>
   <div class="main">
   		<h1 style="text-align:center;">DDNS Daemon Download</h1>
		<h4 style="text-align:center;">net.nsysu.edu.tw</h4>
        <table class="table" style="margin:auto;">
        	<tr>
            	<td style="text-align:center;"><a href="dist/daemon.c"><img src="img/code.png"></a><br>請按右鍵「另存目標」</td>
            </tr>
        </table>
   </div>
    <div class="main" style="padding:10px; line-height:1.8;">
        <h1 style="text-align:center;">DDNS Daemon Readme</h1>
        <h4 style="text-align:center;">net.nsysu.edu.tw</h4>
        <p style="font-size:16px; color:#F90;">使用須知：</p>
        1.請先確認你已經有註冊一個hostname（可以從個人domain管理頁面確認）</font> <br />
        2.電腦需有可編譯C程式的環境，以及 <font color="#FF0000">libssl-dev libcurl4-openssl-dev</font> 的編譯函式庫</font> <br />
        3.hostname不可以超過20個字元<br />
        <p style="font-size:16px; color:#F90;">編譯程式：</p>
        1.編譯指令： <font color="#3333FF">gcc -o ddns_daemon daemon.c -lcrypto -lcurl</font><br />
        3.執行daemon： <font color="#3333FF">sudo ./ddns_daemon</font><br />  <br /> 
        <p style="font-size:16px; color:#F90;">開機自動執行： (Use Ubuntu as example)</p>
        請在 <font color="#3333FF">/etc/rc.local</font> 加上（加在exit;上方）：
        <pre><font color="#FF0000">screen -d -m 程式放置目錄/ddns_daemon</font></pre>
        <p style="font-size:16px; color:#F90;">如果程式執行異常、想要登出原有帳號：</p>
        執行指令： <font color="#3333FF">rm -f /dnsd/.login /dnsd/.last_ip_record /dnsd/tmp_nsupdate</font>
    </div>
    <p>&nbsp;</p>
	<div class="main">
    	<h1 style="text-align:center;">Update Logs</h1>
		<h4 style="text-align:center;">net.nsysu.edu.tw</h4>
        <ul>
        	<li>2013.08.08 Change structure and release source code</li>
        	<li>2013.07.28 Change Daemon SQL connection to HTTP connection</li>          
        </ul>
    </div>
</body>
</html>
