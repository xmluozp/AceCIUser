<?php

// only to satisfy codeigniter code:
define('BASEPATH', "");
define('ENVIRONMENT', "");
require "application/config/database.php";
require "application/config/constants.php";



$df_hostname = $db['default']['hostname'];
$df_database = $db['default']['database'];
$df_username = $db['default']['username'];
$df_password = $db['default']['password'];


if($_POST)
{
	
	$hostname = trim(filter_input(INPUT_POST, 'hostname'));
	$databasename = trim(filter_input(INPUT_POST, 'databasename'));
	$username = trim(filter_input(INPUT_POST, 'username'));
	$password = trim(filter_input(INPUT_POST, 'password'));
	$user_table = trim(filter_input(INPUT_POST, 'user_table'));
	$role_table = trim(filter_input(INPUT_POST, 'role_table'));
	$org_table = trim(filter_input(INPUT_POST, 'org_table'));
	
	
	define('DB_DSN','mysql:host=' . $hostname. ';dbname='. $databasename);
	define('DB_USER',$username);
	define('DB_PASS',$password);  
	
	try {
		$db = new PDO(DB_DSN, DB_USER, DB_PASS);
	} catch (PDOException $e) {
		print "Error: " . $e->getMessage();
		die();
	}
	
	$query = "DROP TABLE ".$user_table .";";
	$query .= "DROP TABLE ".$role_table. ";";
	$query .= "DROP TABLE ".$org_table. ";";
	
	
	$statement = $db->prepare($query);
	$statement->execute();			
	
	// user_token: generated after login, used to decode the token
	// user_token_key: used to match the token from client side
	
	
	/* 
	user status:
	0: inactive
	1: active
	2: reset password
	*/
	$query = "CREATE TABLE ". $user_table ." (
		user_id 		INT(11)	AUTO_INCREMENT PRIMARY KEY,
		user_email		VARCHAR(50) NOT NULL,
		user_password	VARCHAR(255),
		user_created 	DATETIME DEFAULT CURRENT_TIMESTAMP,
		user_active		BOOLEAN  DEFAULT 1,
		user_active_code VARCHAR(12),
		user_group_id	INT(11) DEFAULT 1,
		organization_id	INT(11) DEFAULT 0,
		user_last_login DATETIME DEFAULT CURRENT_TIMESTAMP,
		user_token		VARCHAR(350), 
		user_token_key	VARCHAR(350), 
		user_token_reset_password	VARCHAR(350),
		is_deleted		BOOLEAN DEFAULT 0
	);";
	
	$query .= "CREATE TABLE ". $role_table ." (
		user_group_id 		INT(11)	PRIMARY KEY,
		user_group_name		VARCHAR(50),
		user_group_level	INT(11)
	);";
	
	$query .= "CREATE TABLE ". $org_table ." (
		organization_id 		INT(11) PRIMARY KEY,
		organization_name		VARCHAR(64),
		organization_logo		VARCHAR(64)
	);";
	
	$statement = $db->prepare($query);
	$statement->execute();		
	
	// insert the super admin user
	$query =  "INSERT INTO " . $org_table."(organization_id, organization_name) VALUES(1, 'Ace Space');";
	$query .=  "INSERT INTO " . $org_table."(organization_id, organization_name) VALUES(2, 'Red River College');";
	
	$query .=  "INSERT INTO " . $user_table."(user_email, user_password, user_group_id) VALUES('administrator@aceciuser.none', '', 2);";
	
	$query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(0, 'Visitor', 0);";
	$query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(1, 'Normal User', 1);";
	$query .=  "INSERT INTO ".$role_table. "(user_group_id, user_group_name,user_group_level) VALUES(2, 'Administrator', 2);";
	
	$statement = $db->prepare($query);
	$statement->execute();		
}

?>
<!doctype html>
<html lang = "en">
	<head>
		<meta charset="utf-8"/>
		<title>Ace CI User Management</title>
		
		<style>
		form p{line-height: 20px;}
		form p label{display: inline-block;width: 250px;}
		
		
		</style>
	</head>
		<body>
		<form action="" method="post">

			<p><label>database hostname:</label> <input type="text" readonly name="hostname" value="<?=$df_hostname?>"/></p>
			<p><label>database name:</label> <input type="text" readonly name="databasename" value="<?=$df_database?>"/></p>
			<p><label>database username:</label> <input type="text" readonly name="username" value="<?=$df_username?>"/></p>
			<p><label>database password:</label> <input type="text" readonly name="password" value="<?=$df_password?>"/></p>
		
			<p><label>Name of the User Table:</label> <input type="text" readonly name="user_table" value="<?=TABLE_USER?>"/></p>
			<p><label>Name of the Role Table:</label> <input type="text" readonly name="role_table" value="<?=TABLE_USER_GROUP?>"/></p>
			<p><label>Name of the Organization Table:</label> <input type="text" readonly name="org_table" value="<?=TABLE_ORG?>"/></p>
			
			
			<button>Generate</button>
		</form>
		</body>
</html>

