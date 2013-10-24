<?php

	class LoginSystem {

		/**
		Class variables
		**/

		//Error Messages
		private $SystemErrors = array(
										10  => "User NotFound",
										107 => "NotFound Error !"
									);

		//Date and Time
		private $LocalTime;

		//Sign Cookies to store the expiration time
		private $CookiesName = "lgn_expiretime";

		//Session name for the credential storage
		public $SessionName = "lgn_sys_usr";

		//Expiration time in minutes
		public $ExpireTime = 20;//min

		//The logout page URL
		public $LogoutRedirect = "";

		//The login page URL
		public $LoginRedirect = "";

		//The after login page URL
		public $LoginAfterRedirect = "panel";


		/**
		Login Variables
		**/		

		//Unique field for userid
		private $UserID = "";

		//Table Name (Login Information)
		public $Login_TableName = "user_members";

		//The name of fields using for checking login information
		public $Login_Fields = array('UserName','PassWord');


		/**
		Permission Variables
		**/	

		//Check Permission status
		public $Permission_Status = false;

		//Table Name (User Permission)
		public $Permission_TableName = "user_Permission";

		//Permission unique field for connect between userinfo and Permission table
		public $Permission_UniqueField = "UserID";

		//Permission code field
		public $Permission_CodeField = "Permission";

		//The Permission denied link URL
		public $Permission_Denied = "http://localhost/loginclass/?denied";



		/**
		Class Loging
		**/			

		//...
		public function __construct(){
			$this->LocalTime = time();
			@session_start();
			LoginSystem::SessionCheck();
			LoginSystem::CookieCheck();
			LoginSystem::Check_ExpireTime();
		}

		//Check exist session
		private function SessionCheck(){ 
			$SessionExist = isset($_SESSION[$this->SessionName]) ? $_SESSION[$this->SessionName] : 1;
			if( $SessionExist == 1 ){
				$_SESSION[$this->SessionName] = ""; //Create Session;
				header('Location: '.$this->LoginRedirect);
			}
		}

		//Check exist cookie
		private function CookieCheck(){
			$CookieExist = isset($_COOKIE[$this->CookiesName]) ? $_COOKIE[$this->CookiesName] : NULL;
			if( ($CookieExist == NULL) OR ($CookieExist == "") ){
				$Return_ExpireTime = LoginSystem::Return_ExpireTime();
				if(!setcookie($this->CookiesName, $Return_ExpireTime, $Return_ExpireTime)){
					header('Location: '.$this->LoginRedirect);
				} //Create Cookie (Expire Time)
			}
		}

		//Check Session Expire Time
		private function Check_ExpireTime(){
			if($this->LocalTime > $_COOKIE[$this->CookiesName]){
				LoginSystem::Logout(1);
			} else {
				return true;
			}
		}

		// Calculate Expire Time
		private function Return_ExpireTime(){
			return $this->LocalTime+($this->ExpireTime*60); //to Sec.
		}

		//Logout function
		public function Logout($Redirect = 0){
			unset($_SESSION[$this->SessionName]);
			unset($_COOKIE[$this->CookiesName]);
			if($Redirect == 0){
				header('Location: '.$this->LogoutRedirect);
			} elseif($Redirect == 1){
				header('Location: '.$this->LoginRedirect);
			}
			exit;
		}

		public function PermissionStatus($Status){
			switch($Status){
				case true:
					$this->Permission_Status = true;
				break;
				case false:
				default:
					$this->PermissionStatus = false;
				break;
			}
		}

		//Check user information
		public function Request_Login(){
			$Query = "";
			$RequestInfo = array();
			foreach($this->Login_Fields as $Key => $Field){
				$RequestInfo[$Field] = mysql_real_escape_string(func_get_arg($Key));
				$Query .= "AND ".$Field." = '".$RequestInfo[$Field]."' ";
			}

			$Verify = LoginSystem::VerifyInformation($this->Login_Fields[0],$this->Login_TableName,substr($Query, 3));
			if($Verify[0] == 0){
				return $this->SystemErrors[10];
			} elseif($Verify[0] == 1) {
				LoginSystem::SetLogin($RequestInfo);
			} else {
				return $this->SystemErrors[107];
			}
		}

		//Find Mysql
		private function VerifyInformation($Field,$Table,$Query,$SaveID = 0){
			if($SaveID == 1)
				$SaveField = ",".$this->Permission_UniqueField;
			return mysql_fetch_array(mysql_query("SELECT COUNT(".$Field.")".@$SaveField." FROM ".$Table." WHERE ".$Query));
		}

		//Set Login Information.
		private function SetLogin($Args){
			$Information = implode('[*%LGN%*]',$Args);
			$_SESSION[$this->SessionName] = $Information;
			header('Location: '.$this->LoginAfterRedirect);
			exit;
		}

		//Check Logged.
		//LevelStart : 1
		public function CheckLogin($Permission = 0){
			$Data = explode('[*%LGN%*]',$_SESSION[$this->SessionName]);		
			$Query = "";
			$RequestInfo = array();
			foreach($this->Login_Fields as $Key => $Field){
				$RequestInfo[$Field] = mysql_real_escape_string($Data[$Key]);
				$Query .= "AND ".$Field." = '".$RequestInfo[$Field]."' ";
			}
			if($this->Permission_Status == true){
				$UniqueID = 1;
			} else {
				$UniqueID = 0;
			}
			$Verify = LoginSystem::VerifyInformation($this->Login_Fields[0],$this->Login_TableName,substr($Query, 3), $UniqueID);

			if($Verify[0] != 1) {
				header('Location: '.$this->LoginRedirect);
				exit;
			}

			//Permission
			if($Permission != 0 && $this->Permission_Status == true){
				$this->UniqueID = $Verify[1];
				LoginSystem::PermissionCheck($Permission);
			}

		}

		//Permission Check.
		private function PermissionCheck($Level){
			$Query = $this->Permission_CodeField." = '$Level' AND ".$this->Permission_UniqueField." = '".$this->UniqueID."' ";
			$Verify = LoginSystem::VerifyInformation($this->Permission_UniqueField,$this->Permission_TableName,$Query);
			if($Verify[0] == 0){
				header('Location: '.$this->Permission_Denied);
				exit;
			}
		}


	}





?>