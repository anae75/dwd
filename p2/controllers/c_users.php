<?php
class users_controller extends base_controller {

  public function __construct() 
  {
    parent::__construct();
  } 

  public function signup() 
  {
    # Setup view
    $this->template->content = View::instance('v_users_signup');
    $this->template->title   = "Signup";
    # Render template
    echo $this->template;
  }

  public function p_signup() {
    $newuser = new MyUser();
    if(!$newuser->valid($_POST)) {
      $this->template->set_global('errors', $newuser->errors);
      # var_dump($_POST);
      $this->signup();
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

  public function login() {
    # Setup view
    $this->template->content = View::instance('v_users_login');
    $this->template->title   = "Login";
    # Render template
    echo $this->template;
  }

  public function p_login() {
          
    # Hash submitted password so we can compare it against one in the db
    $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
    
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
      Router::redirect("/users/login");
    } else {  # But if we did, login succeeded! 
      # Store this token in a cookie
      @setcookie("token", $token, strtotime('+1 year'), '/');

      # Send them to the main page - or whever you want them to go
      Router::redirect("/");
    }
  }

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

    #echo "You have been logged out.";
    #echo "old token=" .$token. "<br>";
    #echo "new token=" .$new_token . "<br>";

    # back to welcome page
    Flash::set("You have been logged out.  Please visit again!");
    Router::redirect("/"); 
  }

  public function profile($uid) {
    if(!$this->user) {
      Flash::set("Please log in.");
      Router::redirect("/users/login");
      return;
    }

    $profiled_user = $this->user;
    if($uid && $uid != $this->user->user_id) {
      $viewing_self = false;
      if(MyUser::user_exists($uid)) {
        $profiled_user = MyUser::public_user_info_for($uid);
      } else {
        # XXX - user doesn't exist 
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

    $followers = MyUser::followers($profiled_user->user_id);
    $this->template->set_global('followers', $followers);
    $following = MyUser::following($profiled_user->user_id);
    $this->template->set_global('following', $following);
            
    # Render template
    echo $this->template;
  }

  public function mini_profile($uid)
  {
    if(!$this->user || !MyUser::user_exists($uid)) {
      error_response();
      return;
    }
    $profiled_user = MyUser::public_user_info_for($uid);
    $view = View::instance('v_users_mini_profile');
    $view->set("profiled_user", $profiled_user);
    echo $view->render();
  }

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

  private function error_response()
  {
      header('HTTP/1.1 500 Internal Server Error');
  }

  public function follow($user_id) 
  {
    if(!$this->user) {
      Router::redirect("/users/login");
      return;
    }
    if($this->userObj->follow($user_id)) {
      echo "success";
    } else {
      error_response();
    }
  }

  public function unfollow($user_id) 
  {
    if(!$this->user) {
      Router::redirect("/users/login");
      return;
    }
    if(!$this->userObj->unfollow($user_id)) {
      echo "success";
    } else {
      error_response();
    }
  }

} # end of the class
