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
    print_r($_POST);

    # Insert this user into the database
    $user_id = DB::instance(DB_NAME)->insert("users", $_POST);

    # For now, just confirm they've signed up - we can make this fancier later
    echo "You're signed up";
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

    echo "You have been logged out.";
    echo "old token=" .$token. "<br>";
    echo "new token=" .$new_token . "<br>";

  }

  public function profile() {
    # If user is blank, they're not logged in, show message and don't do anything else
    if(!$this->user) {
      echo "Members only. <a href='/users/login'>Login</a>";
      
      # Return will force this method to exit here so the rest of 
      # the code won't be executed and the profile view won't be displayed.
      return false;
    }
    
    # Setup view
    $this->template->content = View::instance('v_users_profile');
    $this->template->title   = "Profile of".$this->user->first_name;
            
    # Render template
    echo $this->template;
  }

  public function index() {
    if(!$this->user) {
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

  public function follow($user_id) 
  {
    if(!$this->user) {
      Router::redirect("/users/login");
      return;
    }
    $this->userObj->follow($user_id);
  }

} # end of the class
