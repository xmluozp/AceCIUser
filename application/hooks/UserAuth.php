<?php
class UserAuth {
	private $CI;
	private $AuthList = array();

	/**
	 * ManageAuth constructor.
	 */
	public function __construct() {
		$this->CI = &get_instance();

		// set auth in helper, just make codes together
		$authArray = variables_get_auth();
		foreach($authArray as $obj)
		{
			$this->registerAuth($obj[0],$obj[1],$obj[2], $obj[3]);
		}
	}

	/**
	 * Intercept all controller request, validate the function authorization
	 */
	public function auth() {

		$className = strtoupper($this->CI->router->fetch_class());
		$functionName = strtoupper($this->CI->router->fetch_method());

		// check user group, if its a locked account, the user group is GUEST
		if(!$this->checkAuth($className, $functionName)){

			$errorMessage = rawurlencode("Oops, you are using a function: ". $functionName . " which is not permitted.");

			if($this->CI->input->is_ajax_request())
			{
				$redirectString = "<!DOCTYPE html><html><head><meta http-equiv=\"Refresh\" content=\"0;url=".site_url("/Users/view_login/"). $errorMessage ."\"></head></html>";
				echo $redirectString;
			}
			else
			{
				redirect('/Users/view_login/' . $errorMessage);
			}
			die();
		}
	}

	/**
	 * Register the authorizations to the permission list
	 * @param $className
	 * @param $functionNAme
	 * @param $user_group_id
	 * @param $operator
	 */
	function registerAuth($user_group_id, $className, $functionName, $operator = '>')
	{
		// push an auth array
		$this->AuthList[strtoupper($className)][strtoupper($functionName)][$operator][] = $user_group_id;
	}

	function checkAuth($className, $functionName)
	{
		$returnValue = false;
		$user_group_id = get_user_group_id();
		$user_group_level = get_user_group_level();
		$permissionList = array();

		// if its a locked account, user group is guest
		$ci =& get_instance();
		$ci->load->model('users_model');
		$ifLockedForResetPassword = $ci->users_model->exists(array('user_id'=> get_user_id(), 'user_active' => 0));

		if($ifLockedForResetPassword){
			$user_group_id = GUEST;
			$user_group_level = GUEST;
		}

		// have className, means not public function, need to be checked
		if(array_key_exists ($className, $this->AuthList))
		{
			// check the function(higher priority than *, if there is no function needed to check, go *)
			if(array_key_exists ($functionName, $this->AuthList[$className]))
			{
				// check function
				$permissionList = $this->AuthList[$className][$functionName];
			}
			elseif(array_key_exists ("*", $this->AuthList[$className])) // if *
			{
				// check only class
				$permissionList = $this->AuthList[$className]["*"];
			}

			$ci2 =& get_instance();
			$ci2->load->model('user_groups_model');

			// if there is < or >, check(pass any will be passed)
			if(array_key_exists("<", $permissionList))
			{
				$user_group_list = $permissionList["<"];

				// less than smallest number of group_level
				$returnValue = $returnValue || $user_group_level < $ci2->user_groups_model->read_max_group_level($user_group_list);
			}
			// if there is < or >, check
			if(array_key_exists(">", $permissionList))
			{
				$user_group_list = $permissionList[">"];

				// greater than largest number of group_level
				$returnValue = $returnValue || $user_group_level > $ci2->user_groups_model->read_min_group_level($user_group_list);
			}
			// if there is < or >, check
			if(array_key_exists(">=", $permissionList))
			{
				$user_group_list = $permissionList[">="];

				// greater than largest number of group_level
				$returnValue = $returnValue || $user_group_level >= $ci2->user_groups_model->read_min_group_level($user_group_list);
			}
			// if there is < or >, check
			if(array_key_exists("=", $permissionList))
			{
				$returnValue = $returnValue || in_array($user_group_id, $permissionList["="]);
			}
		}
		else // does not need to be checked
		{
			$returnValue = true;
		}

		return $returnValue;
	}
}