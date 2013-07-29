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
        <table class="table">
        	<tr>
            	<td style="text-align:center;"><a href="dist/ddns_daemon_linux.tar"><img src="img/tux.png"></a></td>
                <td style="text-align:center;"><a href="dist/ddns_daemon_rPi.tar"><img width="130" src="img/rPi.png"></a></td>
            </tr>
        </table>
   </div>
    <div class="main" style="padding:10px; line-height:1.8;">
        <h1 style="text-align:center;">DDNS Daemon Readme</h1>
        <h4 style="text-align:center;">net.nsysu.edu.tw</h4>
        <p style="font-size:16px; color:#F90;">安裝須知：</p>
        1.本工具指令會額外在你的電腦安裝兩個套件：<font color="#FF0000">dnsutils, screen</font><br />
        2.請先確認你已經有註冊一個hostname（可以從個人domain管理頁面確認）</font> <br />
        3.hostname不可以超過20個字元<br />
        <p style="font-size:16px; color:#F90;">安裝步驟：</p>
        1.請自行選取一個位置解壓縮檔案包： <font color="#3333FF">tar -xvf ddns_daemon_(依版本自行更改).tar</font><br />
        2.請下「<font color="#3333FF">sudo make install</font>」指令 (目前只適用Debian 系列Linux系統，其他系統需使用emerge or yum）<br />
        3.請依據平台執行daemon: <font color="#3333FF">sudo ./ddaemon_(x86 or x64)</font><br />  <br /> 
        <p style="font-size:16px; color:#F90;">開機自動執行：</p>
        請在 <font color="#3333FF">/etc/rc.local</font> 加上（加在exit;上方）：
        <pre><font color="#FF0000">screen -d -m 解壓縮後的目錄/ddaemon_(x86 or x64 or rPi)</font></pre>
        <p style="font-size:16px; color:#F90;">移除工具：</p>
        執行指令: <font color="#3333FF">sudo make uninstall</font>
        <p style="font-size:16px; color:#F90;">如果程式執行異常：</p>
        執行指令: <font color="#3333FF">make clean</font>
    </div>
    <p>&nbsp;</p>
	<div class="main">
    	<h1 style="text-align:center;">Update Logs</h1>
		<h4 style="text-align:center;">net.nsysu.edu.tw</h4>
        2013.07.28 Change Daemon SQL connection to HTTP connection
    </div>
</body>
</html>
