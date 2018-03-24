<?php


class Users extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		$this->load->model('users_model');
		$this->load->model('user_groups_model');
		$this->load->model('organizations_model');
		
		$this->load->library('form_validation');

		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('cookie');
		
		$this->load->helper('User_email');
		$this->load->helper('User_utilities');
		$this->load->helper('User_variables');
	}

	/**
	 * List all users
	 */
	public function index()
	{
		$this-> view_userList();
	}

	public function view_userList_by_id($id = "")
	{
		$this-> view_userList(array("user_id" => $id));
	}


	/**
	 * view the user List page
	 */
	private function view_userList($conditions = array())
	{
		$data['title'] = 'User List';
		$data['nav'] = get_nav();

		/*==========initial drop downs===========*/
		$data['organizations'] =  json_encode($this->organizations_model->read_list_as_level_dropdown(get_organization_id(), get_user_group_level()));
		$data['user_groups'] =  json_encode($this->user_groups_model->read_list_as_level_dropdown(get_user_group_level()));

		$data['initSearchData'] = json_encode($conditions);

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_index', $data);
		$this->load->view('users/form_modal_user.php', $data);
		$this->load->view('users/inc_footer');
	}

	/**
	 * read users to generate a data grid
	 */
	public function ajax_listPaging()
	{
		// grab getings
		$datatable_varibles = helper_datatable_varibles($this->input->get());

		// generate the JSON going to return to AJAX
		$returnAJAX = $this->users_model->read_datatable(0, get_user_id(), $datatable_varibles);

		echo json_encode($returnAJAX);
	}

	/*
	 * ====================================================================
	 * login display and functions
	 */
	/**
	 * @param string $errorMessages = if get an error when login, errors are shown on the top
	 * @param string $json_error = if get an error before login, errors are shown under textbox
	 */
	public function view_login($errorMessages = "", $json_error = "")
	{
		$data['title'] = 'User Login';
		$data['errorMessages'] = $errorMessages;
		$data['json_error'] = $json_error;

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_full_asset', $data);
		$this->load->view('users/page_login', $data);
		$this->load->view('users/inc_footer');
	}

	public function form_login()
	{
		$user_email = $this->input->post('user_email');
		$user_password = $this->input->post('user_password');
		$remember_me =  $this->input->post('remember_me');

		// 3 attemptings
		if(!isset($_SESSION['login_attempting']))
		{
			$_SESSION['login_attempting'] = 0;
		}
		$isShowCaptcha = $_SESSION['login_attempting'] >= LOGIN_ATTEMPTING_LIMIT;

		// validation
		$v_data['user_email'] = $user_email;
		$v_data['user_password'] = $user_password;
	
		$this->form_validation->set_rules('user_email', 'email', 'trim|required|valid_email');
		$this->form_validation->set_rules('user_password', 'password', 'required|trim');
				
		if($isShowCaptcha)
		{
			$v_data['captcha'] = $this->input->post('captcha');
			$this->form_validation->set_rules('captcha', 'captcha', 'required|callback_validate_captcha');
			$this->form_validation->set_message('validate_captcha', "Wrong Captcha  ". $_SESSION["captcha"]);
		}
		
		$this->form_validation->set_data($v_data);
		
		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] === FALSE)
		{
			$json_error = json_encode($validation_result);

			$this->view_login("", $json_error);
		}
		else
		{
			// sign_auth is in Permission_helper.php, used to sign the authorization to an user
			$user_id = sign_auth($user_email, $user_password, !empty($remember_me));

			if($user_id)
			{		
				$_SESSION['login_attempting'] = 0;
				redirect('/'. LOGIN_REDIRECTION);
			}
			else
			{
				if($_SESSION['login_attempting'] < LOGIN_ATTEMPTING_LIMIT){$_SESSION['login_attempting'] += 1;}
				$this->view_login(rawurlencode("Wrong username or password. Attemptings: " . $_SESSION['login_attempting']));
			}
		}
	}
	
	/**
	 * The first page of login
	 */
	public function view_login_success()
	{
		$data['title'] = 'Success';
		
		$isActive = $this->users_model->check_active(get_user_id());
		
		if($isActive)
		{
			$data['welcome'] = "Welcome, " . get_user_email() . "<br/>";
			$data['welcome'] .= "Your User Id is: " . get_user_id() . "<br/>";
			$data['welcome'] .= "Your User group Id is: " . get_user_group_id() . "<br/>";
			$data['welcome'] .= "Your User group Name is: " . get_user_group_name() . "<br/>";
			$data['welcome'] .= "which is level " . get_user_group_level() . "<br/>";			
			$data['welcome'] .= "your organization id is:" . get_organization_id() . "<br/>";					
			$data['welcome'] .= "your organization name is:" . get_organization_name() . "<br/>";						
			$data['welcome'] .= "your organization logo is:" . get_organization_logo() . "<br/>";				
		}
		else
		{
			$data['welcome'] = "Please active your account before you can use it";
		}
		
		$data['nav'] = get_nav();
		
		$this->load->view('users/inc_header', $data);
		
		if($isActive)
		{
			$this->load->view('users/inc_navigation');
		}
		
		$this->load->view('users/inc_full_asset', $data);
		$this->load->view('users/page_home', $data);
		$this->load->view('users/inc_footer');
	}
	
	public function view_home()
	{
		$data['title'] = 'Home';
		
		$data['nav'] = get_nav();
		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/inc_full_asset', $data);
		$this->load->view('users/page_home', $data);
		$this->load->view('users/inc_footer');
	}
	
	
	/*
	 * ====================================================================
	 * signup display and functions
	 */
	/**
	 * @param string $errorMessages = if get an error when login, errors are shown on the top
	 * @param string $json_error = if get an error before login, errors are shown under textbox
	 */
	public function view_signup($errorMessages = "", $json_error = "")
	{
		$data['title'] = 'User Signup';
		$data['errorMessages'] = $errorMessages;
		$data['json_error'] = $json_error;

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_full_asset', $data);
		$this->load->view('users/page_signup', $data);
		$this->load->view('users/inc_footer');
	}	
		
	public function form_signup()
	{
		// for extension: the project doing email validation, user will be inactive when signup, otherwise change it to FALSE.
		$is_email_inform = TRUE;

		// for extension: if this project doing organizations, need more functions to get the org id
		$organization_id = 1;

		$user_email		 = $this->input->post('user_email');
		$user_password	 = $this->input->post('user_password');
		$user_confirm	 = $this->input->post('user_confirm');
		$user_group_id = NORMAL_USER;
		
		$validata = array(
			'user_email' 		=> $user_email,
			'user_password'		=> $user_password,	
			'user_confirm'		=> $user_confirm
		);
		
		$data = array(
			'user_email' 		=> $user_email,
			'user_password'		=> $user_password,
			'user_created' 		=> date("Y-m-d H:i:s"),
			'user_active' 		=> !$is_email_inform,
			'organization_id' 	=> $organization_id,
			'user_group_id' 	=> $user_group_id,
			'is_deleted' 		=> false
		);
		
		// validation
		$this->form_validation->set_data($validata);
		$this->form_validation->set_rules('user_email', 'email', 'trim|required|valid_email|is_unique['.TABLE_USER.'.user_email]');

		$this->form_validation->set_rules('user_password', 'password', 'required|trim|callback_validate_match_password');
		$this->form_validation->set_rules('user_confirm', 'password', 'required|trim|callback_validate_match_password');
		$this->form_validation->set_message('validate_match_password', 'Your password has to be match.');
		
		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] === FALSE)
		{
			$json_error = json_encode($validation_result);

			$this->view_signup("", $json_error);
		}
		else
		{
			
			$data['user_active_code'] = generate_tokenKey();
			$data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);

			// send the form to proccessing code
			$result = $this->users_model->create($data);

			if($result && $is_email_inform)
			{
			
				$callback_url = site_url("users/func_active/" . $result . "/".$data['user_active_code']);
				
				// The content be modified from User_variables_helper.php
				$email['email_message'] = variables_emails("sign_up", $user_email, array($callback_url));			
				$email['email_subject'] = 'Do-Not-Reply: Your account has been created';
				$email['email_to'] = $user_email;
				
				// call function in User_email_helper
				send_email($email);
				
				$data['type'] = 1;
				$data['title'] = "Account Created";
				$data['messages'] = 'Your account has been created. Please active your account.';	
				
				$this->load->view('users/inc_header', $data);
				$this->load->view('users/inc_full_asset', $data);
				$this->load->view('users/page_message', $data);
				$this->load->view('users/inc_footer');
			}
		}
	}	 
	
	public function func_active($id, $key)
	{
		$result = $this->users_model->active($id, $key);
		$data['title'] = 'User Active';
		$data['type'] = 1;
		$data['messages'] = '';	
		
		if($result)
		{		
			$data['type'] = 1;
			$data['messages'] = 'Active Successed';
			$data['nav'] = get_nav();
		}
		else
		{
			$data['type'] = 0;
			$data['messages'] = 'Active Failed';	
		}
		
		$this->load->view('users/inc_header', $data);
		if($result)
		{
			$this->load->view('users/inc_navigation');
		}
		
		$this->load->view('users/inc_full_asset', $data);
		$this->load->view('users/page_message', $data);
		$this->load->view('users/inc_footer');
	}

	 
	/*
	 * ====================================================================
	 * forgot password display and functions
	 */
	public function view_forgot($errorMessages = "", $json_error = "")
	{
		$data['errorMessages'] = $errorMessages;
		$data['json_error'] = $json_error;

		$data['title'] = 'Forgot Password - Send Email';
		$data['nav'] = get_nav();

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_forgot', $data);
		$this->load->view('users/inc_footer');
	}

	public function view_forgot2($args)
	{
		$data['title'] = 'Forgot Password - Email Has Been Sent';
		$data['nav'] = get_nav();
		$data['user_email'] = $args['user_email'];

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_forgot2', $data);
		$this->load->view('users/inc_footer');
	}

	public function view_forgot3($email="", $key = "", $expired = FALSE, $json_error = "")
	{
		$data['json_error'] = $json_error;
		$data['title'] = 'Change Password - Change Password';
		$data['nav'] = get_nav();
		$data['errorMessages'] = '';
		$data['user_email'] = rawurldecode($email);
		$data['user_token_key'] = $key;

		$token = ($this->users_model->read_from_email($data['user_email']))["user_token_reset_password"];

		$key = rawurldecode($key);

		// try decode the token
		$tokenDecoded = Token::decode_token($token, $key);
		$expired = $expired || !$tokenDecoded;

		// if it's going to reset password
		$isResetPassword = $this->users_model->exists(
			array('user_email'=> $data['user_email'],
				'user_token_reset_password IS NOT NULL' => null));

		$expired = $expired || !$isResetPassword;

		if($expired)
		{
			$data['errorMessages'] = "Request Expired";
		}

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_forgot3', $data);
		$this->load->view('users/inc_footer');
	}

	public function view_forgot4()
	{
		$data['title'] = 'Forgot Password - Success';
		$data['nav'] = get_nav();

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_forgot4', $data);
		$this->load->view('users/inc_footer');
	}

	/**
	 * send email of: forgot password verification code
	 */
	public function form_forgot_sendEmail()
	{
		$user_email = $this->input->post('user_email');

		// validation
		$v_data['user_email'] = $user_email;

		$this->form_validation->set_data($v_data);
		$this->form_validation->set_rules('user_email', 'email', 'trim|required|valid_email');

		// call a function: validate_emailExists to validate the email
		$this->form_validation->set_rules('user_email', 'user_email', 'callback_validate_emailExists');
		$this->form_validation->set_message('validate_emailExists', 'Sorry, its a wrong email.');

		
		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] === FALSE)
		{
			$json_error = json_encode($validation_result);
			$this->view_forgot("",$json_error);
		}
		else
		{
			$callback = 'users/view_forgot3';
			$this->func_sendResetPasswordEmail($user_email, $callback);

			$this->view_forgot2(array("user_email" => $user_email));
		}
	}

	/**
	 * After click in from the email, fill in password, then will change the password
	 */
	public function form_forgot_changePassword()
	{
		$user_email = $this->input->post('user_email');
		//$user_password = $this->input->post('user_password');

		$user_token_key = $this->input->post('user_token_key');
		$user_new_password = $this->input->post('user_password');
		$user_confirm = $this->input->post('user_confirm');
		$remember_me = $this->input->post('remember_me');

		// validation
		$v_data['user_password'] = $user_new_password;
		$v_data['user_confirm'] = $user_confirm;

		$this->form_validation->set_data($v_data);

		$this->form_validation->set_rules('user_password', 'password', 'required|trim|callback_validate_match_password');
		$this->form_validation->set_rules('user_confirm', 'password', 'required|trim|callback_validate_match_password');
		
		$this->form_validation->set_message('validate_match_password', 'Your password has to be match.');

		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] === FALSE)
		{
			$json_error = json_encode($validation_result);

			$this->view_forgot3($user_email, $user_token_key,FALSE , $json_error);
		}
		else
		{
			// if its not a resetting password and not an already-login user:
			$isResetPassword = $this->users_model->exists(
				array('user_email'=> $user_email,
					'user_token_reset_password IS NOT NULL' => null));

			// check the token
			$token = ($this->users_model->read_from_email($user_email))["user_token_reset_password"];
			$tokenDecoded = Token::decode_token($token, $user_token_key);
			
			$expired = !$tokenDecoded;

			if($isResetPassword && !$expired)
			{
				$this->users_model->update_password(password_hash($user_new_password, PASSWORD_DEFAULT), array('user_email' => $user_email), false);

				$user_id = sign_auth($user_email, $user_new_password, !empty($remember_me));

				$this->view_forgot4();
			}
			else
			{
				$this->view_forgot3($user_email, $user_token_key,TRUE);
			}
		}
	}

	/**
	 *
	 * @param string $errorMessages
	 * @param string $json_error
	 */
	public function view_changePassword($errorMessages = "", $json_error = "")
	{
		$data['errorMessages'] = $errorMessages;
		$data['json_error'] = $json_error;

		$data['title'] = 'Change Password - Send Email';
		$data['nav'] = get_nav();

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_change_password', $data);
		$this->load->view('users/inc_footer');
	}

	public function view_changePassword2($args)
	{
		$data['title'] = 'Change Password - Email Has Been Sent';
		$data['nav'] = get_nav();
		$data['user_email'] = $args['user_email'];

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_change_password2', $data);
		$this->load->view('users/inc_footer');
	}

	public function view_changePassword3($email="", $key = "", $expired = FALSE, $json_error = "")
	{

		$data['json_error'] = $json_error;
		$data['title'] = 'Change Password - Change Password';
		$data['nav'] = get_nav();
		$data['errorMessages'] = '';

		$token = ($this->users_model->read(get_user_id()))["user_token_reset_password"];
		$key = rawurldecode($key);

		// try decode the token
		$tokenDecoded = Token::decode_token($token, $key);

		$expired = $expired || !$tokenDecoded;

		if($expired)
		{
			$data['errorMessages'] = "Request Expired";
		}
		else
		{
			$data['user_email'] = $tokenDecoded["email"];
			$data['user_token_reset_password'] = $key;
		}

		$this->load->view('users/inc_header', $data);
		$this->load->view('users/inc_navigation');
		$this->load->view('users/page_change_password3', $data);
		$this->load->view('users/inc_footer');
	}

	public function form_changePassword_sendEmail()
	{
		$user_email = ($this->users_model->read(get_user_id()))["user_email"];

		if ($user_email)
		{
			$callback = 'users/view_changePassword3';
			$this->func_sendResetPasswordEmail($user_email, $callback);
			$this->view_changePassword2(array("user_email" => $user_email));
		}
	}

	/**
	 * The only entrance is right side of navigation bar, will be logout and clear the "remember me"
	 */
	public function func_logout()
	{
		$this->session->sess_destroy();	
		delete_cookie("token","");
		
		// clear token from database
		$this->users_model->update_delete_token(get_user_id());
		$this->view_login();
	}
	
	public function func_kick_out($message)
	{
		$this->session->sess_destroy();	
		delete_cookie("token","");
		
		// clear token from database
		$this->users_model->update_delete_token(get_user_id());
		$this->view_login($message);
	}

	private function func_sendResetPasswordEmail($user_email, $callback_base_url)
	{
		// generate a new password for this user
		$payload=array(
			'iss' => "http://randomtransport.com", //who
			'iat' => $_SERVER['REQUEST_TIME'], //when
			'exp' => $_SERVER['REQUEST_TIME'] + Token::token_resetPassword_expiry(),
			'tmnl' => "web",
			'email'=> $user_email
		);

		$pass_reset_code = $this->func_randomPassword();
		//$pass_reset_code = "uebb5k6RWyLS";

		$token = Token::encode_token($payload , $pass_reset_code);

		// update new password to database
		$this->users_model->update_reset_password($token, array('user_email' => $user_email));

		// generate a link
		$callback_url = site_url($callback_base_url . "/" . rawurlencode($user_email). "/".rawurlencode($pass_reset_code));
		
		// The content be modified from User_variables_helper.php
		$email['email_message'] = variables_emails("reset_password", $user_email, array($callback_url));				
		$email['email_subject'] = 'Do-Not-Reply: Password Reset For Randomtransport Application';
		$email['email_to'] = $user_email;

		// call function in Email_helper
		send_email($email);
	}

	/**
	 * todo: new password will be generated then email to user
	 */
	public function ajax_create()
	{
		$organization_id = $this->input->post('organization_id');

		$user_email		 = $this->input->post('user_email');
		$user_password	 = $this->input->post('user_password');

		$is_email_inform = filter_var($this->input->post('is_email_inform'), FILTER_VALIDATE_BOOLEAN);

		$data = array(
			'user_email' 		=> $user_email,
			'user_password'		=> $user_password,
			'user_created' 		=> date("Y-m-d H:i:s"),
			'user_active' 		=> filter_var($this->input->post('user_active'), FILTER_VALIDATE_BOOLEAN),
			'organization_id' 	=> $organization_id,
			'user_group_id' 	=> $this->input->post('user_group_id'),
			'is_deleted' 		=> false
		);

		// validation
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('user_email', 'email', 'trim|required|valid_email|is_unique['.TABLE_USER.'.user_email]');
		$this->form_validation->set_rules('user_password', 'password', 'required|trim');

		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] !== FALSE)
		{

			$data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);

			// send the form to proccessing code
			$result = $this->users_model->create($data);

			if($result && $is_email_inform)
			{
				
				// The content be modified from User_variables_helper.php
				$email['email_message'] = variables_emails("create_user", $user_email, array($user_password));			
				$email['email_subject'] = 'Do-Not-Reply: Your account has been created';
				$email['email_to'] = $user_email;

				// call function in Email_helper
				send_email($email);
			}
		}
		
		echo json_encode($validation_result);
	}

	/**
	 * AJAX update the user's detail from ajax
	 */
	public function ajax_update()
	{
		$organization_id = $this->input->post('organization_id');

		$user_id = $this->input->post('user_id');

		// keys have to be the same
		$data = array(
			'user_email' 		=> $this->input->post('user_email'),
			'organization_id' 	=> $organization_id,
			'user_active' 		=>  filter_var($this->input->post('user_active'), FILTER_VALIDATE_BOOLEAN)
		);

		if(strlen($this->input->post('user_group_id'))>0)
		{
			$data['user_group_id'] = $this->input->post('user_group_id');
		}
		// only change password if we need
		if(strlen($this->input->post('user_password')) > 0)
		{
			$data['user_password'] = $this->input->post('user_password');
			$data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);
		}

		// validation
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('user_email', 'email', 'required');

		//================== above is codeigniter things==================
		$validation_result = func_run_with_ajax($this->form_validation);
	
		if ($validation_result["success"] !== FALSE)
		{
			// send the form to proccessing code
			$this->users_model->update($user_id, $data);
		}

		echo json_encode($validation_result);
	}

	/**
	 * AJAX getting information
	 */
	public function ajax_fullNameList()
	{
		$keyword = $this->input->post('keyword');
		$list = $this->users_model->read_list_by_fullname($keyword, get_organization_id(), 0);

		echo json_encode($list);
	}

	/**
	 * AJAX read one user's data, return as JSON
	 */
	public function ajax_userDetails()
	{
		$user_id = $this->input->post("id");

		// todo: need to change, only permitted data be retrived
		$returnAJAX = $this->users_model->read_form($user_id);

		echo json_encode($returnAJAX);
	}

	/**
	 *  AJAX activate or lock the user, the active users will be check from hooks/ManageAuth.php: checkAuth
	 *  locked user's permission is the same as GUEST
	 */
	public function ajax_switchActive()
	{
		$user_id = $this->input->post('user_id');

		// todo: need to change, only permitted data be changed
		$this->users_model->switch_active($user_id);
	}

	public function ajax_userDelete()
	{
		$user_id = $this->input->post('user_id');

		// todo: need to change, only permitted data be retrived
		$this->users_model->delete($user_id);
		//add_log("/Users/Delete, id:" . $user_id, USERLOG_DELETE_EVENT);
	}

	public function ajax_randomPassword() {
		echo $this->func_randomPassword();
	}

	public function ajax_emailExists() {

		$ajaxText = "";

		$user_email = $this->input->post('user_email');
		$is_valid_email = filter_var($user_email, FILTER_VALIDATE_EMAIL);
		$email_exists = $this->users_model->check_email_exists($user_email);

		$ajaxText = $is_valid_email?$ajaxText:"It is an invalid email.";
		$ajaxText = $email_exists?"Your email account already exists in our system;":$ajaxText;

		echo $ajaxText;
	}

	/**
	 * @return string: generate a random password for those user forgot password, or when created by admin
	 */
	private function func_randomPassword() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 12; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

	// customized validations============================================

	/*
	 * validate if the email exists in accounts(not contact emails)
	 */
	public function validate_emailExists($str)
	{
		return $this->users_model->check_email_exists($str);
	}

	public function validate_match_password($str)
	{
		return $this->input->post('user_password') == $this->input->post('user_confirm');
	}
	
	public function validate_captcha($str){
		return $this->session->userdata("captcha") == $str;
	}
	
	/*
	 * clear session for test
	 */
	public function test_clear_session()
	{
		$this->session->sess_destroy();
		$this->view_login();
	}
}
