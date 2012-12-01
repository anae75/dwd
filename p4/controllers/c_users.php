<?php
class users_controller extends base_controller {

  public function __construct() 
  {
    parent::__construct();
  } 

  # 
  # Display signup screen
  # side effects: no
  # 
  public function signup() 
  {
    if($this->user) {
      Flash::set("You are already logged in.");
      Router::redirect("/streams");
      return;
    }
    # Setup view
    $this->template->content = View::instance('v_users_signup');
    $this->template->title   = "Signup";
    # Render template
    echo $this->template;
  }

  #
  # Sign up a user
  # XXX side effects: yes (relying on DB::insert to sanitize input)
  #
  public function p_signup() {
    if($this->user) {
      Flash::set("You are already logged in.");
      Router::redirect("/streams");
      return;
    }
    $newuser = new MyUser();
    if(!$newuser->valid($_POST)) {
      Flash::set("Signup failed: " . $newuser->error_message());
      Router::redirect("/users/signup");
      return;
    }

    # Encrypt the password  
    $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

    # More data we want stored with the user        
    $_POST['created']  = Time::now();
    $_POST['modified'] = Time::now();
    $_POST['token']    = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());

    # Dump out the results of POST to see what the form submitted
    # print_r($_POST);

    # Insert this user into the database
    $user_id = DB::instance(DB_NAME)->insert("users", $_POST);

    Flash::set("Thanks for signing up.  Please log in now.");
    Router::redirect("/users/login");
  }        

  #
  # Display edit screen for user
  # side effects: no
  #
  public function edit()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }
    $this->template->content = View::instance('v_users_edit');
    $this->template->title   = "Edit User Settings";
    echo $this->template;
  }

  #
  # Update user input
  # XXX side effects: yes
  #
  public function p_edit()
  {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }
    Helper::csrf_protect($_POST);
    if($this->userObj->update($_POST)) {
      Flash::set("Your settings have been saved.");
      Router::redirect("/");
    } else {
      Flash::set("Update failed: " . $this->userObj->error_message());
      Router::redirect("/users/edit");
    }
  }

  # 
  # Display login screen
  # side effects: no
  # 
  public function login() {
    if($this->user) {
      Flash::set("You are already logged in.");
      Router::redirect("/streams");
      return;
    }
    # Setup view
    $this->template->content = View::instance('v_users_login');
    $this->template->title   = "Login";
    # Render template
    echo $this->template;
  }

  # 
  # Log in a user
  # XXX side effects: yes
  # 
  public function p_login() {
    if($this->user) {
      Flash::set("You are already logged in.");
      Router::redirect("/streams");
      return;
    }

    # Hash submitted password so we can compare it against one in the db
    $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

    $_POST = DB::instance(DB_NAME)->sanitize($_POST);
    
    # Search the db for this email and password
    # Retrieve the token if it's available
    $q = "SELECT token 
            FROM users 
            WHERE email = '".$_POST['email']."' 
            AND password = '".$_POST['password']."'";
    
    $token = DB::instance(DB_NAME)->select_field($q);       
                            
    # If we didn't get a token back, login failed
    if(!$token) {
      # Send them back to the login page
      Flash::set("Your login information was not recognized.  Please try again.");
      Router::redirect("/users/login");
    } else {  # But if we did, login succeeded! 
      Helper::reset_session();
      Helper::csrf_init();
          
      # Store this token in a cookie
      @setcookie("token", $token, strtotime('+1 year'), '/');

      # Send them to the main page - or whever you want them to go
      Router::redirect("/");
    }
  }

  #
  # Log out the current user
  # XXX side effects: yes
  # TODO --> csrf vulnerability here <--
  #
  public function logout() {
    if(!$this->user) {
      Router::redirect("/users/login");
      return;
    }

    # Generate and save a new token for next login
    $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

    # Create the data array we'll use with the update method
    # In this case, we're only updating one field, so our array only has one entry
    $data = Array("token" => $new_token);

    # Do the update
    $token = $this->user->token;
    DB::instance(DB_NAME)->update("users", $data, "WHERE token = '".$token."'");

    # Delete their token cookie - effectively logging them out
    setcookie("token", "", strtotime('-1 year'), '/');

    # back to welcome page
    Flash::set("You have been logged out.  Please visit again!");
    Router::redirect("/"); 
  }

  # 
  # Show user info
  # side effects: no
  # 
  public function profile($uid=null) {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }
    $uid = DB::instance(DB_NAME)->sanitize($uid);

    $profiled_user = $this->user;
    if($uid && $uid != $this->user->user_id) {
      $viewing_self = false;
      if(MyUser::user_exists($uid)) {
        $profiled_user = MyUser::public_user_info_for($uid);
      } else {
        echo "User not found";
        return;
      }
    } else {
      $viewing_self = true;
    }

    # Setup view
    $this->template->content = View::instance('v_users_profile');
    $this->template->title   = "Profile for ".$profiled_user->first_name;

    $this->template->set_global('profiled_user', $profiled_user);
    $this->template->set_global('viewing_self', $viewing_self);

    # Render template
    echo $this->template;
  }

  # 
  # Show user info
  # side effects: no
  # 
  public function mini_profile($uid)
  {
    if(!$this->user || !MyUser::user_exists($uid)) {
      Helper::send_error();
      return;
    }
    $uid = DB::instance(DB_NAME)->sanitize($uid);
    $profiled_user = MyUser::public_user_info_for($uid);
    $view = View::instance('v_users_mini_profile');
    $view->set("profiled_user", $profiled_user);
    echo $view->render();
  }

  #
  # Show all users
  # side effects: no
  #
  public function index() {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    # Setup view
    $this->template->content = View::instance('v_users_index');
    $this->template->title   = "Users";

    $users = MyUser::users();
    $this->template->set_global('users', $users);
            
    # Render template
    echo $this->template;
  }

} # end of the class
