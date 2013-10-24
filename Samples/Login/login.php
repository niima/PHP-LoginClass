<?php
	
	include_once('../../Config/config.php');
	include_once('./sample-config.php');

	if(isset($_REQUEST['login'])){
		print $Login->Request_Login($_REQUEST['username'],$_REQUEST['password']);
	}


?>

<form method='post'>
	<input type="text" name="username"/>
	<input type="text" name="password"/>
	<input type="submit" value="login" name="login"/>
</form>