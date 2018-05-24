If you are using Scotch Box (https://box.scotch.io) you'll need to upgrade your version of MySQL. 
        From the Windows command prompt within your Scotch Box folder:
	        vagrant ssh
		sudo apt-get update
                sudo apt-get update
                sudo apt-get upgrade
		sudo apt-get install mysql-server-5.6
		exit

Clone the ACECIUser project into your HTTP server's root folder:
        If you are using ScotchBox you will want to:
	        Delete the public folder within your Scotch Box folder
		Use git or GitKraken to clone https://github.com/bitprojectspace/AceCIUser.git in the ScotchBox folder.
		Rename the new clone AceCIUser folder to: public

Pull in the required Composer dependencies:
        Install Composer (https://getcomposer.org/) to Windows or to the ScotchBox VM.
	At a command prompt from the project root folder (the "public" folder when using Scotch Box):
	        composer require mailgun/mailgun-php php-http/guzzle6-adapter php-http/message

Set the base URL: (Every CodeIgniter project needs to set this.)
	Config/config.php
		Change to your base url (Where #.#.#.# is your development server's IP address or domain.)
			$config['base_url'] = 'http://#.#.#.#/';
			
Setup the database:
	Config/database.php
		Database has to be set, for example:
			'hostname' => 'localhost',
			'username' => 'johnuser',
			'password' => '123',
			'database' => 'aceciuser'

	Ensure that you have a database in your database server with the same name as the one used in your config/database.php file:
	        From your database SQL prompt (for example):
		        CREATE database aceciuser;
			
	Open the page /aceciuser_setting.php
		Hit the button, generate the tables

Test the login:
        Open the page index.php/users/view_login.php
	        Default admin credentials are:
		username: admin@rrc.none
		password: rrc12345

Update routes: (optional)
		You can set the login page as the first page of your app.
		For example, edit your config/routes.php
		$route['default_controller'] = 'users/view_login';	


Set the auto email service: (This doesn't need to be configured right away.)
	We are using Mailgun service:
		See mailgun.com website
		Set your mail service key
			Helpers/User_email_helper.php

Customize your user permissions	
	
    Helpers/User_variables_helper.php
        Here to set your user permissions (permission to access controller functions)
        Open this php file. The first function, variables_get_auth() is a list of user permissions. Anyone without permission will be kicked out.
		One statement for one permission.
		
        There are 4 arguments.
            1: The role level, not id
            2: The Controller name needs to be checked
            3: The Function name to be checked, * is wildcard means all functions in this controller. The wildcard checking has lower priority.
            4: Operator, could be =, >, <, >=, <=
				For example, $returnValue[] = array(VISITOR, "Users","*", ">=");
	
				Means anyone want to run any function in Users, has to be a VISITOR or higher(VISITOR here is 0 level, means everyone)
	
----------------------------	
For additional

    About Captcha
        The constant LOGIN_ATTEMPTING_LIMIT is the login try times before the Captcha occurred. Right now is 2 just for testing purpose.
        Please at least set it to 5.
        There are some other picture generate settings in Captcha.php

    You can customize your navigation bar for different user groups. (if you don't have your own navigation bar)
        It's in
            Helpers/User_variables_helper.php
                variables_get_navigation()
                    Set the full navigation bar
                variables_get_navigation_permissions()
                    Set the different user group's navigation bar
                
    You can set what should be put in the email notification
        It's in
            Helpers/User_variables_helper.php
                variables_emails()
                
    If you want additional columns for a user, and you want to keep the User Management page rather than use your own, then you need to modify MVC:
        M:
            Models/Users_model.php
                read_datatable()
                    Change what columns should be read for the user list
                    the extra search part will process the Advanced Search request from User List page
					
		V:
			View/users/page_index.php
				Find the <table> tag with id="list_users", add what column you want, have to match the column name from the Model. If its not matching, that <td> fileds will be blank.
				
				Those attributes start with "data-" are the annotation of columns, if you want to use the Datatables in your project, feel free to do so.
					*  data-source: --- name of the column you are looking for
					*  data-orderby-desc
					*  data-orderby-asc: --- if has this attribute, will be as default sorting
					*  data-filter: --- what columns will be respond by the filter
					*  data-icon: ---   if its an icon column. (the value=>icon settings are in create_datatable.js)
					*  data-class: ---  change the column's classname
					*  data-render: --- change content, need a render function code below. The render function arguments have to be like: fn(data, type, row, meta). The return value will be filled into the <td> tag
					*  data-multiselect: --- set the column as multi checkbox column
			View/users/form_modal_user.php
				The divs userDetailModal and userCreateModal are the form of create or edit an user by the administrator.
				If you add any item, please keep the attribute "data-validation", otherwise this item will not be submitted when you hit "save" button.
				Those attributes start with "data-" are annotations.
					*  data-not-retrive : will be keep empty when getting data
					*  data-validation : will be validated when submitting data
					*  data-render : Instead of fill-in, the system will call a function. The function format have to be like: fn(itemSelector, value)
					
		C:		
			Controllers/Users.php
				form_signup()
					What information will be stored when user signup
				ajax_create()
					What information will be stored when the administrator create an user
				ajax_update()
					Columns of admin-update an user
				
			Becareful, In order to prevent anonymous requests, functions here are better been set (in the permission list). 

	If you need more user groups (roles), you need to change 3 places:
		Database
			insert a record to user_groups
			change your admin account's user groups(if you changed the group id of admin user)
		Config/constants.php
						
		Helpers/User_variables_helper.php
			Give the new role a customized navigation bar
		
		---- but do not delete the Visitor group, it will be used for the "level = 0" situation.