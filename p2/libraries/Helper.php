<?php 

class Helper
{
  public static function csrf_init()
  { 
    $csrf_token = Utils::generate_random_string(10);
    $_SESSION["csrf_token"] = $csrf_token;
  }

  public static function csrf_protect(&$data)
  {
    if(!isset($_SESSION["csrf_token"]) || !isset($data["csrf_token"]) || ($_SESSION["csrf_token"] != $data["csrf_token"] )) {
      Helper::send_error();
      die("csrf");
    }
    unset($data["csrf_token"]);
    return $data;
  }

  public static function csrf_protect_ajax()
  {
    if(!isset($_SESSION["csrf_token"]) || !isset($_SERVER['HTTP_X_CSRF_TOKEN']) || ($_SESSION["csrf_token"] != $_SERVER['HTTP_X_CSRF_TOKEN']) ) {
      Helper::send_error();
      die("csrf");
    }
  }

  public static function csrf_hidden_field()
  {
    return sprintf("<input type='hidden' name='csrf_token' value='%s'>", Helper::csrf_token());
  }

  public static function csrf_token()
  { 
    # the session may have expired
    if(!isset($_SESSION["csrf_token"])) {
      Helper::csrf_init();
    }
    return $_SESSION["csrf_token"];
  }

  public static function send_error()
  {
      header('HTTP/1.1 500 Internal Server Error');
  }

}
