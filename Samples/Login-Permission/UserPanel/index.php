<?php
	include_once('../../../Config/config.php');
	include_once('../sample-config.php');

	/*
		10 -> the page permission code (4example. 1, 20, 300, 40 or more code for permission)
	*/
	$Login->CheckLogin();
?>

<h1>Welocome :)</h1>
<a href='../logout.php'>Logout</a>
<a href='./module2.php'>Permission Page</a>