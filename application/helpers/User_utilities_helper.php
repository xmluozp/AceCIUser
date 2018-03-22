<?php
//================================================================ Global current user variables
	/**
	 * will be used everywhere when need a current user's id or other information
	 * if its empty, will try to renew the login status by token
	 */
	function get_user_id()
	{
		$returnValue = 0;

		if(isset($_SESSION['user_id']))
		{
			$returnValue = $_SESSION['user_id'];
		}
		elseif(isset($_COOKIE["token"]) && $_COOKIE["token"])
		{
			$returnValue = (continue_token($_COOKIE["token"]))["user_id"];
		}

		return $returnValue;
	}
	
	function get_user_email()
	{
		$returnValue = "anonymous";

		if(isset($_SESSION['user_email']))
		{
			$returnValue = $_SESSION['user_email'];
		}
		
		return $returnValue;
	}

	function get_organization_id()
	{
		$returnValue = 0;

		if(isset($_SESSION['organization_id']))
		{
			$returnValue = $_SESSION['organization_id'];
		}
		elseif(isset($_COOKIE["token"]) && $_COOKIE["token"])
		{
			$returnValue = (continue_token($_COOKIE["token"]))["organization_id"];
		}

		return $returnValue;
	}

	function get_user_group_id()
	{
		$user_id = get_user_id();

		if($user_id > 0)
		{
			$ci =& get_instance();
			$query = $ci->db->query("SELECT user_group_id  FROM users WHERE user_id = ". $user_id);
			$returnValue= $query->row()->user_group_id;
		}
		else
		{
			$returnValue = 0;
		}

		return $returnValue;
	}

	function get_user_group_level()
	{
		$user_id = get_user_id();
		if($user_id > 0) {
			$ci =& get_instance();
			$query = $ci->db->query("SELECT user_group_level  FROM users join user_groups on users.user_group_id = user_groups.user_group_id WHERE users.user_id = " . $user_id);
			$returnValue = $query->row()->user_group_level;
		}
		else
		{
			$returnValue = 0;
		}
		return $returnValue;
	}

	function get_user_group_name()
	{
		$user_id = get_user_id();
		if($user_id > 0) {
			$ci =& get_instance();
			$query = $ci->db->query("SELECT user_group_name  FROM users join user_groups on users.user_group_id = user_groups.user_group_id WHERE users.user_id = " . $user_id);
			$returnValue = $query->row()->user_group_name;
		}
		else
		{
			$returnValue = 0;
		}
		return $returnValue;
	}
//================================================================ Tokens
	/**
	 * will be called by login, generate a token if user choose "remember", store to cookie
	 */
	function sign_auth($user_email, $user_password, $isRemember = FALSE)
	{
		$returnValue = 0;
		$tokenString = NULL;
		$tokenKey = NULL;

		$ci =& get_instance();
		$ci->load->model("users_model");

		$user = Users_model::check_password($user_email, $user_password);

		if($user) {

			if($isRemember)
			{
				$tokenKey = generate_tokenKey();

				$tokenString = generate_token($user->user_id, $tokenKey);
			}

			// what will be stored in session
			$userData = array(
				'user_id' => $user->user_id,
				'user_email' => $user_email,
				'user_group_name' => $user->user_group_name,
				'organization_id' => $user->organization_id,
			);
			$ci->session->set_userdata($userData);

			// update database login information, including token
			Users_model::update_the_token($user->user_id, $tokenString, $tokenKey);

			$returnValue = $user->user_id;
		}

		return $returnValue;
	}

	/**
	 * renew the token: check the token for user, then keep this user login
	 * @param $token
	 */
	function continue_token($token)
	{
		$ci =& get_instance();
		$ci->load->model("users_model");

		// match the token
		$getTokenKey = Users_model::get_tokenKey($token);
		$tokenArray = Token::decode_token($token, $getTokenKey);

		// get user from token payload
		$user_id = $tokenArray["uid"];
		$user = Users_model::check_token($user_id, $token);

		// check expiry
		$isExpiry = $tokenArray["exp"] < $_SERVER['REQUEST_TIME'];

		// login the user
		if($user && !$isExpiry)
		{
			// generate a new token to continue the login status
			$tokenKey = generate_tokenKey() ; //generate_tokenKey();
			$tokenString = generate_token($tokenArray["uid"], $tokenKey);

			$userData = array(
				'user_id' => $user_id,
				'user_email' => $user->user_email,
				'user_group_name' => $user->user_group_name,
				'organization_id' => $user->organization_id,
			);

			$ci->session->set_userdata($userData);

			Users_model::update_the_token($user->user_id, $tokenString, $tokenKey);
			return array("user_id" => $user_id, "organization_id" => $user->organization_id);
		}
		else
		{
			return array("user_id" => 0, "organization_id" => 0);
		}
	}

	/**
	 * called by above functions, generate a token then set it to the cookie
	 * @param $uid
	 * @return string
	 */
	function generate_token($uid, $key)
	{
		// generate a token==========
		$payload=array(
			'iss' => "http://randomtransport.com", //who
			'iat' => $_SERVER['REQUEST_TIME'], //when
			'exp' => $_SERVER['REQUEST_TIME'] + Token::token_expiry(),
			'tmnl' => "web",
			'uid'=> $uid
		);
		$tokenString = Token::encode_token($payload, $key);
		setcookie("token", $tokenString, time() + Token::token_expiry(), "/");

		return $tokenString;
	}

	/**
	 * generate a random key for token
	 * @return string
	 */
	function generate_tokenKey()
	{
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array();
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 6; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}

//================================================================ Logs
	/**
	 * called from anywhere, record the log to a table named "Logs"
	 * @param $message
	 * @return bool
	 */
	 /*
	function add_log($message,$logtype, $userId = 0)
	{
		$ci =& get_instance();
		$ci->load->model("user_logs_model");

		$user_id = $userId;
		if(!$user_id)
		{
			$user_id = get_user_id();
		}

		if($user_id)
		{
			// static function
			User_logs_model::add_log($user_id, $message,$logtype);
		}
		else
		{
			return false;
		}
	}
	*/
//================================================================ Navigation bars

	/**
	 * get all permissions for current login user, will be called for build the navigation bar
	 * @return array
	 */
	function get_my_navigation_permissions()
	{
		$user_id = get_user_id();

		if(!$user_id)
		{
			$userGroupId = GUEST;
		}
		else
		{
			$userGroupId = get_user_group_id($user_id);
		}

		// read permissions from session
		$permission_json = file_get_contents(base_url('initial/navigation_roles.json'));
		$permissions_all = json_decode($permission_json, true);

		// get permissions from the user group
		$userGroup = $permissions_all[(string)$userGroupId];
		$userGroupPermissions = $userGroup["Permissions"];

		// get extends from the user group,  get permissions from extend, remove repeating fields(union arrays)
		foreach ($userGroup["Extended"] as $grpId)
		{
			$userGroupPermissions = array_merge($userGroupPermissions, $permissions_all[$grpId]["Permissions"]);
			//$userGroupPermissions += $permissions_all[$grpId]["Permissions"];
		};

		return $userGroupPermissions;
	}

	/**
	 * create a hashes, will be used to generate the navigation bar
	 * @return array
	 */
	function get_nav()
	{
		// original menu from config file. /permission.json
		//$nav_json = file_get_contents(base_url('initial/navigations.json'));
		//$nav = json_decode($nav_json, true);
		$nav = variables_get_navigation();
		// get permit codes allowed for current user
		$myPermissions = get_my_navigation_permissions();

		// filter permissions: remove all permissions not allowed
		foreach($nav as $navKey => $navItem)
		{
			// delete item which is not permitted
			if(!in_array($navItem['FunctionCode'], $myPermissions)){
				unset($nav[$navKey]);
			}
			else
			{
				// check for submenu
				if(array_key_exists('Subitems', $navItem))
				{
					// delete item from submenu which is not permitted
					foreach($navItem['Subitems'] as $subMenuKey => $subMenuValue)
					{
						if(!in_array($subMenuValue['FunctionCode'], $myPermissions)){
							unset($nav[$navKey]['Subitems'][$subMenuKey]);
						}
					}
				}
			}
		}

		return $nav;
	}

//================================================================ Tools
	function dateFormat($date)
	{
		$dateParsed = date_parse_from_format("MM j, yyyy", $date);
		$newDate = $dateParsed["year"]."/".$dateParsed["month"]."/". $dateParsed["day"];
		return $newDate;
	}

	/**
	 * Generate a MySql select string for decode the date formate.
	 * @param $date
	 * @return string
	 */
	function dateFormat_decode($dateColumnName)
	{
		$decode_string = "DATE_FORMAT(".$dateColumnName.", '%M %d, %Y %H:%i')";

		return $decode_string;
	}
	function dateFormat_dateOnly_decode($dateColumnName)
	{
		$decode_string = "DATE_FORMAT(".$dateColumnName.", '%M %d, %Y')";

		return $decode_string;
	}

	function enum_join_statement($db, $table, $column)
	{
		$db->join('enum', '(enum_dir = "'. $table .'/'. $column.'") AND ('.$column.' = enum.enum_code)', 'left');
		//return '(enum_dir = "'. $table .'/'. $column.'") AND ('.$column.' = enum.enum_code)';
	}

	function func_run_with_ajax($form){

		$ajax_return = array();
		$ajax_return["success"] = $form->run();

		if ($ajax_return["success"] === FALSE)
		{
			$form->set_error_delimiters('', '');

			$ajax_return["title"] = 'failure';

			$ajax_return["messages"] = array();
			//===========================

			// loop the input data for validation
			foreach($form->validation_data as $key=>$value)
			{
				if(strlen(form_error($key))>0)
				{
					$ajax_return["messages"][$key] = form_error($key);
					//$validationReturn["messages"][]= array("itemName"=>$key, "message" => form_error($key));
				}
			}
		}
		else
		{
			$ajax_return["title"] = 'success';
		}

		$post_back = isset($_POST) ? $_POST : NULL;

		$ajax_return["post_back"] = $post_back;

		return $ajax_return;
	}
	
	/**
	 * Class Token
	 * The default is 30 days token
	 * can be changed from the function token_expiry and token_resetPassword_expiry
	 */
	Class Token{

		public static function token_expiry()
		{
			return 86400 * 30; // 30 days
		}

		public static function token_resetPassword_expiry()
		{
			return 60 * 10; // 10 minutes
		}

		// encode a token
		public static function encode_token($payload, $key, $alg = 'SHA256')
		{
			$key = md5($key);
			$jwt = self::urlsafeB64Encode(json_encode(array('typ' => 'JWT', 'alg' => $alg))) .
				'.' . self::urlsafeB64Encode(json_encode($payload));

			return $jwt . '.' . self::signature($jwt, $key, $alg);
		}

		public static function signature($input, $key, $alg)
		{
			return hash_hmac($alg, $input, $key);
		}

		// decode a token
		public static function decode_token($jwt, $key)
		{
			$tokens = explode('.', $jwt);
			$key    = md5($key);

			if (count($tokens) != 3)
				return false;

			$header64 = $tokens[0];
			$payload64 = $tokens[1];
			$sign = $tokens[2];

			$header = json_decode(self::urlsafeB64Decode($header64), JSON_OBJECT_AS_ARRAY);

			if (empty($header['alg']))
				return false;

			if (self::signature($header64 . '.' . $payload64, $key, $header['alg']) !== $sign)
				return false;

			$payload = json_decode(self::urlsafeB64Decode($payload64), JSON_OBJECT_AS_ARRAY);

			$time = $_SERVER['REQUEST_TIME'];
			if (isset($payload['iat']) && $payload['iat'] > $time)
				return false;

			if (isset($payload['exp']) && $payload['exp'] < $time)
				return false;

			return $payload;
		}

		private static function urlsafeB64Encode($string)
		{

			return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
		}

		private static function urlsafeB64Decode($string)
		{
			return base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT));
		}

	}
