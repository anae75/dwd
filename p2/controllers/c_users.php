<?php
class users_controller extends base_controller {

  public function __construct() 
  {
    parent::__construct();
    echo "users_controller construct called<br><br>";
  } 

  public function index() {
    echo "Welcome to the users's department";
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

    # Encrypt the password  
    $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

    # More data we want stored with the user        
    $_POST['created']  = Time::now();
    $_POST['modified'] = Time::now();
    $_POST['token']    = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());

    # Dump out the results of POST to see what the form submitted
    print_r($_POST);

    # Insert this user into the database
    $user_id = DB::instance(DB_NAME)->insert("users", $_POST);

    # For now, just confirm they've signed up - we can make this fancier later
    echo "You're signed up";
  }        

  public function login() {
          echo "This is the login page";
  }

  public function logout() {
          echo "This is the logout page";
  }

  public function profile($user_name = NULL) {
          
          if($user_name == NULL) {
                  echo "No user specified";
          }
          else {
                  echo "This is the profile for ".$user_name;
          }
  }

} # end of the class
