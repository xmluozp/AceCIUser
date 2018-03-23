<?php
/**
 * Register the permission to role
 * user level (using constant variable here), class name, function name, operator
 * @return array
 */
function variables_get_auth()
{
	$returnValue = array();
	$returnValue[] = array(VISITOR, "Users","*", ">=");
	$returnValue[] = array(VISITOR, "Users","view_login_success", ">=");
	$returnValue[] = array(VISITOR, "Users","view_forgot", "=");
	$returnValue[] = array(VISITOR, "Users","view_forgot2", "=");
	$returnValue[] = array(VISITOR, "Users","view_forgo3", "=");

	$returnValue[] = array(VISITOR,"Users","index", ">");
	$returnValue[] = array(VISITOR,"Users","view_home", ">");
	
	$returnValue[] = array(VISITOR,"Users","ajax_update", ">");
	$returnValue[] = array(VISITOR,"Users","ajax_create", ">");
	$returnValue[] = array(VISITOR,"Users","ajax_switchActive",  ">");
	$returnValue[] = array(VISITOR,"Users","ajax_userDelete",  ">");

	$returnValue[] = array(VISITOR, "Users","ajax_userDetails",  ">");

	return $returnValue;
}

/**
 * Navigation bar for different users
 * @return array
 */
function variables_get_navigation_permissions()
{
	
	$returnValue = array();
	
	$returnValue["0"] = array(
		"GroupName"	  => "Guest",
		"Extended"	  => array(),
		"Permissions" => array(
			
		)	
	);
	
	$returnValue["1"] = array(
		"GroupName"	  => "Guest",
		"Extended"	  => array(),
		"Permissions" => array(
			"home",
			"usr","usr01",
			"org"
		)	
	);
	
	$returnValue["2"] = array(
		"GroupName"	  => "Guest",
		"Extended"	  => array("1"),
		"Permissions" => array()	
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
		"Target"		=>	"organizations");

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