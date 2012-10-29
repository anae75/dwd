<?php 

# misc helper methods

class Helper
{
  #
  # CSRF protection
  #

  # generate token and store it in the session
  public static function csrf_init()
  { 
    $csrf_token = Utils::generate_random_string(10);
    $_SESSION["csrf_token"] = $csrf_token;
  }

  # die if posted data doesn't have the right token
  public static function csrf_protect(&$data)
  {
    if(!isset($_SESSION["csrf_token"]) || !isset($data["csrf_token"]) || ($_SESSION["csrf_token"] != $data["csrf_token"] )) {
      Helper::send_error();
      die("csrf");
    }
    unset($data["csrf_token"]);
    return $data;
  }

  # die if ajax header doesn't have the right token
  public static function csrf_protect_ajax()
  {
    if(!isset($_SESSION["csrf_token"]) || !isset($_SERVER['HTTP_X_CSRF_TOKEN']) || ($_SESSION["csrf_token"] != $_SERVER['HTTP_X_CSRF_TOKEN']) ) {
      Helper::send_error();
      die("csrf");
    }
  }

  # put the token in a form
  public static function csrf_hidden_field()
  {
    return sprintf("<input type='hidden' name='csrf_token' value='%s'>", Helper::csrf_token());
  }

  # return the current token
  public static function csrf_token()
  { 
    # the session may have expired
    if(!isset($_SESSION["csrf_token"])) {
      Helper::csrf_init();
    }
    return $_SESSION["csrf_token"];
  }

  #
  # return an error
  #

  public static function send_error()
  {
      header('HTTP/1.1 500 Internal Server Error');
  }

  #
  # start a new session (anti-session-fixation)
  #

  public static function reset_session()
  {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]);
      }

    session_destroy();
    session_start();

  }

}
