<?php
class Users_model extends CI_Model {

	//public $tableName;
	public $current_user_group_level;

	private static $db;
	private static $mainTableName;

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		self::$db = &get_instance()->db;
		self::$mainTableName = "users";
		
		$this->load->helper('cookie');
		$this->load->helper('User_email');
		$this->load->helper('User_utilities');
		$this->load->helper('User_variables');
		
		
		$this->current_user_group_level= get_user_group_level();		
	}

	/*
	 * ====data filter====
	 */
	public function data_row_filter()
	{
		self::$db->where(self::$mainTableName.".is_deleted", false);

		if($this->current_user_group_level < ADMINISTRATOR)
		{
			self::$db->where('users.organization_id =', get_organization_id() );
		}
	}
	public function data_column_filter()
	{
		self::$db->select('*');
	}

	/*
	 * ====data filter====
	 */


	//=============================== basic CRUD below
	public function create($data)
	{
		self::$db->insert(self::$mainTableName, $data);
		$insert_id = self::$db->insert_id();

		return $insert_id;
	}

	public function read($id)
	{
		$this->data_row_filter();

		//self::$db->select(dateFormat_decode("user_created")." AS user_created");

		self::$db->select("*");

		$query = self::$db->get_where(self::$mainTableName , array('user_id' => $id));
		return $query->row_array();
	}

	public function read_from_email($email)
	{
		// not use filter, because its used by visiter
		self::$db->select("*");

		$query = self::$db->get_where(self::$mainTableName , array('user_email' => $email));
		return $query->row_array();
	}


	public function update($id, $data)
	{
		self::$db->where('user_id', $id);
		$this->data_row_filter();

		return self::$db->update(self::$mainTableName, $data);
	}

	public function delete($id)
	{
		self::$db->set('is_deleted', TRUE);
		self::$db->where('user_id', $id);
		$this->data_row_filter();

		return self::$db->update(self::$mainTableName);
	}

	/**
	 * Will be used for have-not-login user, so dont need the filter
	 * @param $data
	 * @return mixed
	 */
	public function exists($data)
	{
		self::$db->from(self::$mainTableName);
		self::$db->where($data);
		$count = self::$db->count_all_results();
		return $count;
	}

	//=============================== basic CRUD above
	static public function check_password($user_email, $user_password)
	{
		$db = self::$db;

		$tableName = "users";
		$db->from($tableName);
		$db->join('user_groups', 'user_groups.user_group_id = users.user_group_id');

		$db->where(array('user_email' => $user_email));
		$db->select('user_id');
		$db->select('user_password');
		$db->select($tableName. '.organization_id AS organization_id');
		$db->select('user_groups.user_group_name AS user_group_name');

		$query = $db->get();

		if($query->num_rows() > 0)
		{
			$dataBasePassword = $query -> row()-> user_password;

			if(password_verify($user_password, $dataBasePassword))
			{
				return $query -> row();
			}
		}

		return false;
	}

	//=============================== basic CRUD above
	static public function check_token($user_id, $user_token)
	{
		$tableName = self::$mainTableName;
		$db = self::$db;

		$db->from($tableName);
		$db->join('user_groups', 'user_groups.user_group_id = users.user_group_id');

		$db->where(array('user_id' => $user_id));
		$db->where(array('user_token' => $user_token));

		$db->select('user_email');
		$db->select('user_id');
		$db->select('organization_id');
		$db->select('user_groups.user_group_name AS user_group_name');
		

		$query = $db->get();

		if($query->num_rows() > 0)
		{
			return $query -> row();

		}
		return false;
	}

	public function check_email_exists($user_email)
	{
		self::$db->from(self::$mainTableName);
		self::$db->where(array('user_email' => $user_email));
		$count = self::$db->count_all_results();
		return $count > 0;
	}
	
	public function check_active($user_id)
	{
		self::$db->from(self::$mainTableName);
		self::$db->where(array('user_id' => $user_id));
		self::$db->where(array('user_active' => TRUE));
		$count = self::$db->count_all_results();
		return $count > 0;
	}

	public function read_form($id)
	{
		$this->data_row_filter();
		$this->data_column_filter();
		self::$db->select(dateFormat_decode("user_created")." AS user_created");
		self::$db->select(dateFormat_decode("user_last_login")." AS user_last_login");


		$query = self::$db->get_where(self::$mainTableName , array('user_id' => $id));
		return $query->row_array();
	}

	/*
	 * read users, generate a Datatable
	 *
	 * */
	public function read_datatable($organization_id, $user_id = 0, $datatable_requests)
	{
		// Because it only displays the users whose level lower than current logging in user
		$current_user_level = $this->current_user_group_level;

		// Need to display user group's name, so join to user_groups
		self::$db->from(self::$mainTableName);
		self::$db->join('user_groups', 'user_groups.user_group_id = users.user_group_id');

		// filter for permissions
		$this->data_row_filter();

		// also a filter, but its joining table so need to be here
		if($user_id)
		{
			self::$db->where('user_groups.user_group_level <', $current_user_level );
		}

		// extra search code here--------------------
		// used for advanced search
		$extraSearch = $datatable_requests["extraSearch"];
		if(array_key_exists("search_date_start", $extraSearch))
		{
			self::$db->where('user_created >', dateFormat($extraSearch["search_date_start"]));
		}

		if(array_key_exists("search_date_end", $extraSearch))
		{
			self::$db->where('user_created <', dateFormat($extraSearch["search_date_end"]));
		}

		if(array_key_exists("user_id", $extraSearch))
		{
			self::$db->where('users.user_id', $extraSearch["user_id"]);
		}
		// extra search code here---------------------

		self::$db->select('CONCAT("[", user_groups.user_group_id, "] " ,user_group_name)AS user_group');
		self::$db->select('user_id AS user_id');
		self::$db->select('user_email AS user_email');
		self::$db->select( dateFormat_decode("user_created"). " AS user_created");
		self::$db->select('user_active AS user_active');

		// DT_RowId is necessary for Datatable display
		self::$db->select('user_id AS DT_RowId');

		$returnAJAX = helper_datatable_db(self::$db, self::$mainTableName, $datatable_requests);

		return $returnAJAX;
	}

	/*
	 * Get the group level of an user
	 * */
	/*public function read_user_group_level($user_id)
	{
		//$CI =& get_instance();
		//$CI->load->database();
		//$db = $CI->db;

		$db = self::$db;
		$db-> from(self::$mainTableName);
		$db-> join('user_groups', 'user_groups.user_group_id = users.user_group_id');

		$db->select('user_group_level');
		$db->where('users.user_id' , $user_id);

		$result = $db->get()->row();

		if($result)
		{
			return $result->user_group_level;
		}
		else
		{
			return 0;
		}
	}
*/
	public function switch_active($user_id)
	{
		self::$db->set('user_active', 'NOT user_active', FALSE);
		self::$db->where('user_id', $user_id);
		$this->data_row_filter();

		self::$db->update(self::$mainTableName);
	}
	
	public function active($user_id, $code)
	{
		// user can only active from a code.
		if($code && $user_id)
		{
			self::$db->set('user_active', TRUE);
			self::$db->set('user_active_code', NULL);
			self::$db->where('user_id', $user_id);
			self::$db->where('user_active_code', $code);
			
			self::$db->update(self::$mainTableName);
			return self::$db->affected_rows() > 0;
		}
		else
		{
			return FALSE;
		}	
	}

	/**
	 * Only used on login function
	 * @param $user_id
	 * @param string $tokenstring
	 */
	public function update_token($user_id, $tokenstring = "")
	{
		self::$db->set('user_last_login', "'".date("Y-m-d H:i:s")."'", FALSE);

		if($tokenstring)
		{
			self::$db->set('user_token', '"'.$tokenstring.'"' , FALSE);
		}
		else
		{
			self::$db->set('user_token', "NULL" , FALSE);
		}

		self::$db->where('user_id', $user_id);
		self::$db->update(self::$mainTableName);
	}

	/**
	 * Be used in permission_helper, update current token to renew the login expiry
	 * @param $user_id
	 * @param string $tokenstring
	 */
	static public function update_the_token ($user_id, $tokenString = "", $tokenKey = "")
	{
		$tableName = self::$mainTableName;
		$db = self::$db;
		$db->set('user_last_login', "'".date("Y-m-d H:i:s")."'", FALSE);

		if($tokenString)
		{
			$db->set('user_token', '"'.$tokenString.'"' , FALSE);
			$db->set('user_token_key', '"'.$tokenKey.'"' , FALSE);
		}
		else
		{
			$db->set('user_token', "NULL" , FALSE);
			$db->set('user_token_key', "NULL" , FALSE);
		}
		$db->where('user_id', $user_id);
		$db->update($tableName);
	}

	static public function get_tokenKey($tokenString)
	{
		$db = self::$db;
		$db->where("user_token", $tokenString);
		$db->select("user_token_key");
		$result = $db->get(self::$mainTableName)->row();

		if($result)
		{
			$returnValue = $result->user_token_key;
		}
		else
		{
			$returnValue = "";
		}
		return $returnValue;
	}

	public function update_password($newPassword, $data = array(), $isForgot = false)
	{
		self::$db->set('user_password', "'".$newPassword ."'", FALSE);

		// if its called from "forgot password" process, lock the account
		self::$db->set('user_reset_password', 'NULL' , FALSE);

		self::$db->where($data);

		self::$db->update(self::$mainTableName);
	}

	public function update_reset_password($resetPass, $data = array())
	{
		self::$db->set('user_reset_password', "'".$resetPass ."'", FALSE);

		self::$db->where($data);

		self::$db->update(self::$mainTableName);
	}
}



