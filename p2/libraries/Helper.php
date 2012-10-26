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
      die("csrf");
    }
    unset($data["csrf_token"]);
    return $data;
  }

  public static function csrf_token()
  {
    return sprintf("<input type='hidden' name='csrf_token' value='%s'>", $_SESSION["csrf_token"]);
  }

}
