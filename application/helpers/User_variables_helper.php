<?php
/**
 * Register the permission to role
 * user level (using constant variable here), class name, function name, operator
 * @return array
 */
function variables_get_auth()
{
	$returnValue = array();
	
	$returnValue[] = array(VISITOR, "Users","*", ">");
	
	// The functions can accessed by VISITORs:
	$returnValue[] = array(VISITOR, "Users","view_forgot", "=");
	$returnValue[] = array(VISITOR, "Users","view_forgot2", "=");
	$returnValue[] = array(VISITOR, "Users","view_forgo3", "=");
	$returnValue[] = array(VISITOR, "Users","form_forgot_sendEmail", "=");
	$returnValue[] = array(VISITOR, "Users","form_forgot_changePassword", "=");

	
	$returnValue[] = array(VISITOR, "Users","view_login", ">=");
	$returnValue[] = array(VISITOR, "Users","form_login", ">=");
	$returnValue[] = array(VISITOR, "Users","view_login_success", ">=");
	$returnValue[] = array(VISITOR, "Users","view_signup", ">=");
	$returnValue[] = array(VISITOR, "Users","form_signup", ">=");
	$returnValue[] = array(VISITOR, "Users","func_active", ">=");
	
	$returnValue[] = array(VISITOR, "Users","view_login", ">=");
	
	
	
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
			"usr","usr01",
			"org"
		)	
	);
	
	// ADMIN
	$returnValue[2] = array(
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