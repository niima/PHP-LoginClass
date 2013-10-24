<?php

	include_once(__DIR__.'/../../Class/class.login.php');
	$Login = new LoginSystem();

	$Login->LogoutRedirect = "./login.php";
	$Login->LoginRedirect = "../login.php";
	$Login->LoginAfterRedirect = "./UserPanel/";

	//Checking permission
	$Login->PermissionStatus(true);

?>