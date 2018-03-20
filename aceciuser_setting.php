<?php

if($_POST)
{
	
	$hostname = trim(filter_input(INPUT_POST, 'hostname'));
	$databasename = trim(filter_input(INPUT_POST, 'databasename'));
	$username = trim(filter_input(INPUT_POST, 'username'));
	$password = trim(filter_input(INPUT_POST, 'password'));
	$user_table = trim(filter_input(INPUT_POST, 'user_table'));
	$role_table = trim(filter_input(INPUT_POST, 'role_table'));
	
	define('DB_DSN','mysql:host=' . $hostname. ';dbname='. $databasename);
	define('DB_USER',$username);
	define('DB_PASS',$password);  
	
	try {
		$db = new PDO(DB_DSN, DB_USER, DB_PASS);
	} catch (PDOException $e) {
		print "Error: " . $e->getMessage();
		die(); // Force execution to stop on errors.
	} 

	$query = "CREATE TABLE ". $user_table ." (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		email VARCHAR(50),
		reg_date 	DATETIME
	)";
	
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
		
			<p><label>database hostname:</label> <input type="text" name="hostname" value="localhost"/></p>
			<p><label>database name:</label> <input type="text" name="databasename" value="aceciuser"/></p>
			<p><label>database username:</label> <input type="text" name="username" value="serveruser"/></p>
			<p><label>database password:</label> <input type="text" name="password" value="gorgonzola7!"/></p>

		
			<p><label>Name of the User Table:</label> <input type="text" name="user_table" value="users"/></p>
			<p><label>Name of the Role Table:</label> <input type="text" name="role_table" value="user_groups"/></p>
			
			
			<button>Generate</button>
		</form>
		</body>
</html>

