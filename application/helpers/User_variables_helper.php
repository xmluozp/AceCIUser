<?php

/**
 * Register the permission to role
 * user level (using constant variable here), class name, function name, operator
 * @return array
 */
function variables_get_auth()
{
	$returnValue = array();
	$returnValue[] = array(GUEST, "Users","*", ">=");
	$returnValue[] = array(GUEST, "Home","*", ">");
	$returnValue[] = array(GUEST, "Users","view_forgot", "=");
	$returnValue[] = array(GUEST, "Users","view_forgot2", "=");
	$returnValue[] = array(GUEST, "Users","view_forgo3", "=");

	$returnValue[] = array(BUSINESS_MANAGER,"Users","index", ">=");
	$returnValue[] = array(BUSINESS_MANAGER,"Users","ajax_update", ">=");
	$returnValue[] = array(BUSINESS_MANAGER,"Users","ajax_create", ">=");
	$returnValue[] = array(BUSINESS_MANAGER, "Users","ajax_switchActive",  ">=");
	$returnValue[] = array(BUSINESS_MANAGER, "Users","ajax_userDelete",  ">=");

	$returnValue[] = array(DISPATCHER, "Users","ajax_userDetails",  ">=");

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
		"Target"		=>	"Home");


	// -----------------------------orders
	$subitems[] = array(
		"FunctionCode"	=>	"ord01"	,
		"Name"			=>	"Create Order",
		"Target"		=>	"index.php");

	$subitems[] = array(
		"FunctionCode"	=>	"ord02"	,
		"Name"			=>	"View Orders",
		"Target"		=>	"index.php",
		"Divider"		=>	true);

	$returnValue[] = array(
		"FunctionCode"	=>	"ord",
		"Name"			=>	"Orders",
		"Target"		=>	"index.php",
		"Subitems"		=> 	$subitems);
	$subitems = Array();

	// -----------------------------trips

	$subitems[] = array(
		"FunctionCode"	=>	"trp01"	,
		"Name"			=>	"View Trips",
		"Target"		=>	"index.php");

	$subitems[] = array(
		"FunctionCode"	=>	"trp02"	,
		"Name"			=>	"Trip Events",
		"Target"		=>	"index.php");

	$subitems[] = array(
		"FunctionCode"	=>	"trp03"	,
		"Name"			=>	"Trip Logs",
		"Target"		=>	"index.php");

	$subitems[] = array(
		"FunctionCode"	=>	"trp04"	,
		"Name"			=>	"Inspections List",
		"Target"		=>	"index.php",
		"Divider"		=> 	true);

	$returnValue[] = array(
		"FunctionCode"	=>	"trp",
		"Name"			=>	"Trips",
		"Target"		=>	"index.php",
		"Subitems"		=> 	$subitems);

	$subitems = Array();
	// -----------------------------resources

	$subitems[] = array(
		"FunctionCode"	=>	"rsc03"	,
		"Name"			=>	"Trucks"	,
		"Target"		=>	"index.php");
	$subitems[] = array(
		"FunctionCode"	=>	"rsc04"	,
		"Name"			=>	"Axles"	,
		"Target"		=>	"index.php");
	$subitems[] = array(
		"FunctionCode"	=>	"rsc05"	,
		"Name"			=>	"Trailers"	,
		"Target"		=>	"index.php");
	$subitems[] = array(
		"FunctionCode"	=>	"rsc06"	,
		"Name"			=>	"Cargos"	,
		"Target"		=>	"index.php");

	$subitems[] = array(
		"FunctionCode"	=>	"rsc01"	,
		"Name"			=>	"Contacts"	,
		"Target"		=>	"Contacts",
		"Divider"		=> 	true);
	$subitems[] = array(
		"FunctionCode"	=>	"rsc02"	,
		"Name"			=>	"Locations"	,
		"Target"		=>	"Locations");

	$subitems[] = array(
		"FunctionCode"	=>	"rsc07"	,
		"Name"			=>	"Driver's Licenses"	,
		"Target"		=>	"index.php",
		"Divider"		=> 	true);

	$returnValue[] = array(
		"FunctionCode"	=>	"rsc",
		"Name"			=>	"Resources",
		"Target"		=>	"index.php",
		"Subitems"		=> 	$subitems);

	$subitems = Array();

	// -----------------------------users

	$subitems[] = array(
		"FunctionCode"	=>	"usr01"	,
		"Name"			=>	"User Accounts",
		"Target"		=>	"Users");

	$subitems[] = array(
		"FunctionCode"	=>	"usr_lg"	,
		"Name"			=>	"User Logs",
		"Target"		=>	"User_logs");



	$subitems[] = array(
		"FunctionCode"	=>	"usr02"	,
		"Name"			=>	"Customers",
		"Target"		=>	"Customers",
		"Divider"		=> 	true);

	$subitems[] = array(
		"FunctionCode"	=>	"usr03"	,
		"Name"			=>	"Drivers",
		"Target"		=>	"Customers");


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
 * convert updated format to database format
 * @param $date
 * @return string
 */
