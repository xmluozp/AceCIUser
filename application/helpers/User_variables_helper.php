<?php
/**
 * Register the permission to role
 * user level (using constant variable here), class name, function name, operator
 * Here every "Class name" should be the name of a controller
 * @return array
 */
function variables_get_auth()
{
	$returnValue = array();
	
	// Functions can be accessed by VISITORs:
	$returnValue[] = array(VISITOR, "Users","view_forgot", "=");
	$returnValue[] = array(VISITOR, "Users","view_forgot2", "=");
	$returnValue[] = array(VISITOR, "Users","view_forgot3", "=");
	$returnValue[] = array(VISITOR, "Users","form_forgot_sendEmail", "=");
	$returnValue[] = array(VISITOR, "Users","form_forgot_changePassword", ">=");	
	
	$returnValue[] = array(VISITOR, "Users","view_login", ">=");
	$returnValue[] = array(VISITOR, "Users","form_login", ">=");
	$returnValue[] = array(VISITOR, "Users","view_login_success", ">=");
	// - Sign up
	$returnValue[] = array(VISITOR, "Users","view_signup", ">=");
	$returnValue[] = array(VISITOR, "Users","form_signup", ">=");
	$returnValue[] = array(VISITOR, "Users","func_active", ">=");

	$returnValue[] = array(VISITOR, "Users","func_logout", ">=");

	// Permissions of User functions
	$returnValue[] = array(VISITOR, "Users","*", ">");
	
	// Permissions of Organization functions
	$returnValue[] = array(ADMINISTRATOR, "Organizations","*", ">=");
	$returnValue[] = array(ADMINISTRATOR, "Users","view_junks", ">=");
	$returnValue[] = array(ADMINISTRATOR, "Users","form_junks", ">=");
	
	return $returnValue;
}

/**
 * Navigation bar for different users
 * @return array
 */
function variables_get_navigation_permissions()
{
	
	$returnValue = array();
	
	//The index here is the user group ID, not the level. But right now we set them as the same value for convinience.
	// VISITOR
	$returnValue[0] = array(
		"Extended"	  => array(),
		"NavigationList" => array(
		)	
	);
	
	// NORMAL USER
	$returnValue[1] = array(
		"Extended"	  => array(),
		"NavigationList" => array(
			"home",
			"usr", "usr01", "usr02",
			"org"
		)	
	);
	
	// VIP USER
	$returnValue[2] = array(
		"Extended"	  => array("1"),
		"NavigationList" => array()	
	);
	
	// ADMIN
	$returnValue[3] = array(
		"Extended"	  => array("1"),
		"NavigationList" => array()	
	);
	
	return $returnValue;
}

/**
 * All menu items of the navigation bar
 * @return array
 */
function variables_get_navigation(){

	$returnValue = array();

	$returnValue[] = array(
		"FunctionCode"	=>	"home",
		"Name"			=>	"Home",
		"Target"		=>	"users/view_home");

	// -----------------------------users

	$subitems[] = array(
		"FunctionCode"	=>	"usr01"	,
		"Name"			=>	"User Accounts",
		"Target"		=>	"users");  
		
	$subitems[] = array(
		"FunctionCode"	=>	"usr02"	,
		"Name"			=>	"Junk Records",
		"Target"		=>	"users/view_junks",
		"Divider"		=> 	true); // Divider is the line between this item and the item above

	$returnValue[] = array(
		"FunctionCode"	=>	"usr",
		"Name"			=>	"Users",
		"Target"		=>	"",
		"Subitems"		=> 	$subitems);

	// -----------------------------users
	$returnValue[] = array(
		"FunctionCode"	=>	"org",
		"Name"			=>	"Organizations",
		"Target"		=>	"Organizations");

	return $returnValue;
}


/**
 *
 */
function variables_emails($type = 0, $emailAddress = "", $varibles = array())
{
	
	$emailArray[0] = "error";
	
	$emailArray[""] = "";
	
	$emailArray['reset_password'] = "<p style='font-size: 15px; line-height:40px;'>Hello, ".$emailAddress. "<br/>" .
			"Please reset your password through this link: <br/> <a href='". $varibles[0]. "' target='_blank' style='font-size: 24px;'>RESET YOUR PASSWORD</a>".
			"<br/> Or copy and paste thie URL into your browser address bar: <br/>".
			$varibles[0]. "<br/></p>";
	
	$emailArray['create_user'] = "<p style='font-size: 15px; line-height:40px;'>Hello, ".$emailAddress. "<br/>" .
					"Your account has been created. <br/> Your password is: ". $varibles[0] ."</p>";
			
			
	$emailArray['sign_up'] = "<p style='font-size: 15px; line-height:40px;'>Hello, ".$emailAddress. "<br/>" .
				"Your account has been created. <br/> ".				
				"Please active your account through this link: <br/> <a href='". $varibles[0]. "' target='_blank' style='font-size: 24px;'>ACTIVE YOUR ACCOUNT</a>".
				"<br/> Or copy and paste thie URL into your browser address bar: <br/>".
				$varibles[0]. "<br/></p>";
				
	
	return $emailArray[$type];
	
	
}