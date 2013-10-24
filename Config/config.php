<?php
ob_start();
	$AMS = mysql_connect("localhost","root","123456");
	mysql_select_db("loginclass",$AMS);
?>